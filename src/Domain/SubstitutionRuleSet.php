<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * @implements \IteratorAggregate<int, SubstitutionRule>
 */
final readonly class SubstitutionRuleSet implements \IteratorAggregate, \Countable
{
    /** @var list<SubstitutionRule> */
    private array $rules;

    public function __construct(SubstitutionRule ...$rules)
    {
        $this->rules = array_values($rules);
    }

    public function findReplacementFor(int $number): ?string
    {
        $replacementText = '';

        foreach ($this->rules as $rule) {
            if ($rule->matches($number)) {
                $replacementText .= $rule->replacementText;
            }
        }

        return '' === $replacementText ? null : $replacementText;
    }

    public function isEmpty(): bool
    {
        return [] === $this->rules;
    }

    public function count(): int
    {
        return \count($this->rules);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->rules);
    }
}
