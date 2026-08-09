<?php

declare(strict_types=1);

namespace App\Statistics;

interface MostFrequentRequestProviderInterface
{
    public function findMostFrequentRequest(): ?MostFrequentRequest;
}
