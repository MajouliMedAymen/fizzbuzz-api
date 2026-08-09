<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\ConsistentRulePairs;
use Symfony\Component\Validator\Constraints as Assert;

#[ConsistentRulePairs]
final readonly class SequenceRequest
{
    public const int MIN_RULES = 2;

    public const int MAX_RULES = 2;

    public const int MAX_LIMIT = 100_000;

    public const int MAX_REPLACEMENT_LENGTH = 64;

    public function __construct(
        #[Assert\Positive(message: 'int1 must be a strictly positive integer.')]
        public ?int $int1 = null,

        #[Assert\Positive(message: 'int2 must be a strictly positive integer.')]
        public ?int $int2 = null,

        #[Assert\Length(max: self::MAX_REPLACEMENT_LENGTH)]
        public ?string $str1 = null,

        #[Assert\Length(max: self::MAX_REPLACEMENT_LENGTH)]
        public ?string $str2 = null,

        #[Assert\NotNull(message: 'limit is required.')]
        #[Assert\Positive(message: 'limit must be a strictly positive integer.')]
        #[Assert\LessThanOrEqual(
            value: self::MAX_LIMIT,
            message: 'limit must not exceed {{ compared_value }}.'
        )]
        public ?int $limit = null,
    ) {
    }

    /**
     * @return array<int, int|null>
     */
    public function divisorsByPosition(): array
    {
        return [1 => $this->int1, 2 => $this->int2];
    }

    /**
     * @return array<int, string|null>
     */
    public function replacementTextsByPosition(): array
    {
        return [1 => $this->str1, 2 => $this->str2];
    }

    /**
     * @return array{int1: int|null, int2: int|null, limit: int|null, str1: string|null, str2: string|null}
     */
    public function toParameters(): array
    {
        return [
            'int1' => $this->int1,
            'int2' => $this->int2,
            'limit' => $this->limit,
            'str1' => $this->str1,
            'str2' => $this->str2,
        ];
    }
}
