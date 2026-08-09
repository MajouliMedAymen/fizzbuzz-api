<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: 'kernel.exception', priority: 0)]
final readonly class ApiExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.debug%')]
        private bool $debug,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api/')) {
            return;
        }

        $exception = $event->getThrowable();
        $status = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $payload = [
            'type' => 'about:blank',
            'title' => Response::$statusTexts[$status] ?? 'Error',
            'status' => $status,
        ];

        $violations = $this->extractViolations($exception);

        if ([] !== $violations) {
            $payload['title'] = 'Validation failed';
            $payload['detail'] = 'The request parameters are invalid.';
            $payload['violations'] = $violations;
        } elseif ($status < 500) {
            $payload['detail'] = $exception->getMessage();
        } elseif ($this->debug) {
            $payload['detail'] = $exception->getMessage();
        }

        $response = new JsonResponse($payload, $status);
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }

    /**
     * @return list<array{field: string, message: string}>
     */
    private function extractViolations(\Throwable $exception): array
    {
        $previous = $exception->getPrevious();

        if (!$previous instanceof ValidationFailedException) {
            return [];
        }

        $violations = [];

        foreach ($previous->getViolations() as $violation) {
            $violations[] = [
                'field' => $violation->getPropertyPath(),
                'message' => (string) $violation->getMessage(),
            ];
        }

        return $violations;
    }
}
