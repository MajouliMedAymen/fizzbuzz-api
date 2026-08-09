<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\SubstitutionRule;
use App\Domain\SubstitutionRuleSet;
use App\Dto\SequenceRequest;

final class SubstitutionRuleSetFactory
{
    public function createFromRequest(SequenceRequest $request): SubstitutionRuleSet
    {
        $divisorsByPosition = $request->divisorsByPosition();
        $replacementTextsByPosition = $request->replacementTextsByPosition();
        $rules = [];

        foreach ($divisorsByPosition as $position => $divisor) {
            $replacementText = $replacementTextsByPosition[$position] ?? null;
            $hasDivisor = null !== $divisor;
            $hasReplacementText = null !== $replacementText && '' !== $replacementText;

            if (!$hasDivisor && !$hasReplacementText) {
                continue;
            }

            if (!$hasDivisor || !$hasReplacementText) {
                throw new \LogicException(sprintf(
                    'Cannot build rule %d: divisor and replacement text must both be present. '
                    .'This request should have been rejected by ConsistentRulePairs.',
                    $position,
                ));
            }

            $rules[] = new SubstitutionRule($divisor, $replacementText);
        }

        return new SubstitutionRuleSet(...$rules);
    }
}
