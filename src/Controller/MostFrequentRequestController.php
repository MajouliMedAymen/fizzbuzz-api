<?php

declare(strict_types=1);

namespace App\Controller;

use App\Statistics\MostFrequentRequestProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class MostFrequentRequestController
{
    public function __construct(
        private MostFrequentRequestProviderInterface $mostFrequentRequestProvider,
    ) {
    }

    #[Route('/api/v1/statistics', name: 'api_statistics', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $mostFrequentRequest = $this->mostFrequentRequestProvider->findMostFrequentRequest();

        if (null === $mostFrequentRequest) {
            return new JsonResponse(['parameters' => null, 'hits' => 0]);
        }

        return new JsonResponse([
            'parameters' => $mostFrequentRequest->parameters,
            'hits' => $mostFrequentRequest->hitCount,
            'lastHitAt' => $mostFrequentRequest->lastHitAt->format(\DATE_ATOM),
        ]);
    }
}
