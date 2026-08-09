<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class HealthCheckController
{
    public function __construct(
        private Connection $databaseConnection,
    ) {
    }

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $databaseIsReachable = $this->databaseIsReachable();

        return new JsonResponse([
            'name' => 'FizzBuzz API',
            'description' => 'Parameterisable fizz-buzz generator with request statistics.',
            'status' => $databaseIsReachable ? 'ok' : 'degraded',
            'database' => $databaseIsReachable ? 'ok' : 'unreachable',
            'endpoints' => [
                'fizzbuzz' => [
                    'method' => 'GET',
                    'path' => '/api/v1/fizzbuzz',
                    'description' => 'Generates the fizz-buzz sequence for the given rules.',
                    'example' => '/api/v1/fizzbuzz?int1=3&int2=5&str1=fizz&str2=buzz&limit=15',
                ],
                'statistics' => [
                    'method' => 'GET',
                    'path' => '/api/v1/statistics',
                    'description' => 'Returns the most frequently requested parameter set and its hit count.',
                ],
                'health' => [
                    'live' => '/health/live',
                    'ready' => '/health/ready',
                ],
            ],
        ]);
    }

    #[Route('/health/live', name: 'health_live', methods: ['GET'])]
    public function live(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/health/ready', name: 'health_ready', methods: ['GET'])]
    public function ready(): JsonResponse
    {
        if (!$this->databaseIsReachable()) {
            return new JsonResponse(
                ['status' => 'error', 'database' => 'unreachable'],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        }

        return new JsonResponse(['status' => 'ok', 'database' => 'ok']);
    }

    private function databaseIsReachable(): bool
    {
        try {
            $this->databaseConnection->executeQuery('SELECT 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
