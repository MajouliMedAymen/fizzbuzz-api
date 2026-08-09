<?php

declare(strict_types=1);

namespace App\Statistics;

use App\Dto\SequenceRequest;

interface HitRecorderInterface
{
    public function recordHit(SequenceRequest $request): void;
}
