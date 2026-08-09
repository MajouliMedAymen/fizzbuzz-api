<?php

declare(strict_types=1);

namespace App\Statistics\Doctrine;

use App\Repository\RequestStatisticRepository;
use App\Statistics\MostFrequentRequest;
use App\Statistics\MostFrequentRequestProviderInterface;

final readonly class DoctrineMostFrequentRequestProvider implements MostFrequentRequestProviderInterface
{
    public function __construct(
        private RequestStatisticRepository $repository,
    ) {
    }

    public function findMostFrequentRequest(): ?MostFrequentRequest
    {
        $statistic = $this->repository->findMostFrequentlyRequested();

        if (null === $statistic) {
            return null;
        }

        return new MostFrequentRequest(
            parameters: [
                'int1' => $statistic->getFirstDivisor(),
                'int2' => $statistic->getSecondDivisor(),
                'limit' => $statistic->getSequenceLimit(),
                'str1' => $statistic->getFirstReplacementText(),
                'str2' => $statistic->getSecondReplacementText(),
            ],
            hitCount: $statistic->getHitCount(),
            lastHitAt: $statistic->getLastHitAt(),
        );
    }
}
