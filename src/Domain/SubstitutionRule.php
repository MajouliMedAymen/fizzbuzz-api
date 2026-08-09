<?php

declare(strict_types=1);

namespace App\Domain;

final readonly class SubstitutionRule
{
    public function __construct(
        public int $divisor,
        public string $replacementText,
    ) {
        if ($divisor < 1) {
            throw new \InvalidArgumentException(sprintf(
                'A substitution divisor must be strictly positive, %d given.',
                $divisor,
            ));
        }

        if ('' === $replacementText) {
            throw new \InvalidArgumentException('A substitution replacement text must not be empty.');
        }
    }

    public function matches(int $number): bool
    {
        return 0 === $number % $this->divisor;
    }
}
