<?php

declare(strict_types=1);

namespace App\Statistics;

use App\Dto\SequenceRequest;
use Psr\Log\LoggerInterface;

final readonly class FailSafeHitRecorder implements HitRecorderInterface
{
    public function __construct(
        private HitRecorderInterface $decoratedRecorder,
        private LoggerInterface $logger,
    ) {
    }

    public function recordHit(SequenceRequest $request): void
    {
        try {
            $this->decoratedRecorder->recordHit($request);
        } catch (\Throwable $failure) {
            $this->logger->error('Unable to record request statistics.', [
                'exception' => $failure,
                'parameters' => $request->toParameters(),
            ]);
        }
    }
}
