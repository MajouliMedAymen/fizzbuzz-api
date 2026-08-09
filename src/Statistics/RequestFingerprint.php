<?php

declare(strict_types=1);

namespace App\Statistics;

use App\Dto\SequenceRequest;

final readonly class RequestFingerprint implements \Stringable
{
    private function __construct(
        private string $hash,
    ) {
    }

    public static function fromRequest(SequenceRequest $request): self
    {
        return self::fromParameters($request->toParameters());
    }

    /**
     * @param array<string, int|string|null> $parameters
     */
    public static function fromParameters(array $parameters): self
    {
        return new self(hash('sha256', json_encode($parameters, \JSON_THROW_ON_ERROR)));
    }

    public function toString(): string
    {
        return $this->hash;
    }

    public function __toString(): string
    {
        return $this->hash;
    }
}
