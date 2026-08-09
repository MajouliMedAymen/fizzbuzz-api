<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ConsistentRulePairs extends Constraint
{
    public string $countMismatchMessage = 'The number of integers ({{ ints }}) must equal the number of strings ({{ strings }}): each int{n} needs its str{n}.';

    public string $missingReplacementMessage = 'int{{ position }} was provided without str{{ position }}.';

    public string $missingDivisorMessage = 'str{{ position }} was provided without int{{ position }}.';

    public string $gapMessage = 'Rule positions must be contiguous: position {{ position }} is defined while an earlier one is missing.';

    public string $tooFewMessage = 'At least {{ min }} rules are required.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
