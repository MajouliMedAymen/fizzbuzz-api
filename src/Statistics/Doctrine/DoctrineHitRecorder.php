<?php

declare(strict_types=1);

namespace App\Statistics\Doctrine;

use App\Dto\SequenceRequest;
use App\Repository\RequestStatisticRepository;
use App\Statistics\HitRecorderInterface;
use App\Statistics\RequestFingerprint;

final readonly class DoctrineHitRecorder implements HitRecorderInterface
{
    public function __construct(
        private RequestStatisticRepository $repository,
    ) {
    }

    public function recordHit(SequenceRequest $request): void
    {
        $this->repository->incrementHitCount(
            RequestFingerprint::fromRequest($request),
            $request->toParameters(),
        );
    }
}
