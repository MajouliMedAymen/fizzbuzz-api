<?php

declare(strict_types=1);

namespace App\Statistics;

final readonly class MostFrequentRequest
{
    /**
     * @param array<string, int|string|null> $parameters
     */
    public function __construct(
        public array $parameters,
        public int $hitCount,
        public \DateTimeImmutable $lastHitAt,
    ) {
    }
}
