<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\SequenceGenerator;
use App\Dto\SequenceRequest;
use App\Factory\SubstitutionRuleSetFactory;
use App\Statistics\HitRecorderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsController]
final readonly class GenerateSequenceController
{
    private const array ALLOWED_PARAMETERS = ['int1', 'int2', 'str1', 'str2', 'limit'];

    public function __construct(
        private SubstitutionRuleSetFactory $ruleSetFactory,
        private SequenceGenerator $sequenceGenerator,
        private HitRecorderInterface $hitRecorder,
    ) {
    }

    #[Route('/api/v1/fizzbuzz', name: 'api_fizzbuzz', methods: ['GET'])]
    public function __invoke(
        Request $httpRequest,
        #[MapQueryString(validationFailedStatusCode: 422)]
        SequenceRequest $request,
    ): JsonResponse {
        $this->rejectUnknownParameters($httpRequest);

        $ruleSet = $this->ruleSetFactory->createFromRequest($request);
        $sequence = $this->sequenceGenerator->generateAll($ruleSet, (int) $request->limit);

        $this->hitRecorder->recordHit($request);

        return new JsonResponse([
            'parameters' => $request->toParameters(),
            'count' => \count($sequence),
            'result' => $sequence,
        ]);
    }

    private function rejectUnknownParameters(Request $httpRequest): void
    {
        $unexpected = array_diff(array_keys($httpRequest->query->all()), self::ALLOWED_PARAMETERS);

        if ([] === $unexpected) {
            return;
        }

        $violations = new ConstraintViolationList();

        foreach ($unexpected as $parameter) {
            $violations->add(new ConstraintViolation(
                \sprintf('Unknown parameter "%s". Only int1, int2, str1, str2 and limit are accepted.', $parameter),
                null,
                [],
                null,
                (string) $parameter,
                null,
            ));
        }

        throw new HttpException(
            422,
            'The request parameters are invalid.',
            new ValidationFailedException(null, $violations),
        );
    }
}
