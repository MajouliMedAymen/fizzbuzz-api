<?php

declare(strict_types=1);

namespace App\Domain;

final class SequenceGenerator
{
    /**
     * @param positive-int $limit
     *
     * @return \Generator<int, string>
     */
    public function generate(SubstitutionRuleSet $ruleSet, int $limit): \Generator
    {
        for ($number = 1; $number <= $limit; ++$number) {
            yield $ruleSet->findReplacementFor($number) ?? (string) $number;
        }
    }

    /**
     * @param positive-int $limit
     *
     * @return list<string>
     */
    public function generateAll(SubstitutionRuleSet $ruleSet, int $limit): array
    {
        return iterator_to_array($this->generate($ruleSet, $limit), false);
    }
}
