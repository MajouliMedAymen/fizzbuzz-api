<?php

declare(strict_types=1);

namespace App\Validator;

use App\Dto\SequenceRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ConsistentRulePairsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConsistentRulePairs) {
            throw new UnexpectedTypeException($constraint, ConsistentRulePairs::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof SequenceRequest) {
            throw new UnexpectedValueException($value, SequenceRequest::class);
        }

        $divisors = $value->divisorsByPosition();
        $replacementTexts = $value->replacementTextsByPosition();

        $providedDivisorCount = \count(array_filter(
            $divisors,
            static fn (?int $divisor): bool => null !== $divisor,
        ));
        $providedReplacementCount = \count(array_filter(
            $replacementTexts,
            static fn (?string $text): bool => null !== $text && '' !== $text,
        ));

        if ($providedDivisorCount !== $providedReplacementCount) {
            $this->context->buildViolation($constraint->countMismatchMessage)
                ->setParameter('{{ ints }}', (string) $providedDivisorCount)
                ->setParameter('{{ strings }}', (string) $providedReplacementCount)
                ->atPath('rules')
                ->addViolation();
        }

        $completePairs = 0;
        $sawGap = false;

        foreach (array_keys($divisors) as $position) {
            $divisor = $divisors[$position];
            $replacementText = $replacementTexts[$position] ?? null;
            $hasDivisor = null !== $divisor;
            $hasReplacementText = null !== $replacementText && '' !== $replacementText;

            if ($hasDivisor && !$hasReplacementText) {
                $this->context->buildViolation($constraint->missingReplacementMessage)
                    ->setParameter('{{ position }}', (string) $position)
                    ->atPath('str'.$position)
                    ->addViolation();
            }

            if (!$hasDivisor && $hasReplacementText) {
                $this->context->buildViolation($constraint->missingDivisorMessage)
                    ->setParameter('{{ position }}', (string) $position)
                    ->atPath('int'.$position)
                    ->addViolation();
            }

            if ($hasDivisor && $hasReplacementText) {
                if ($sawGap) {
                    $this->context->buildViolation($constraint->gapMessage)
                        ->setParameter('{{ position }}', (string) $position)
                        ->atPath('int'.$position)
                        ->addViolation();
                }

                ++$completePairs;
            } elseif (!$hasDivisor && !$hasReplacementText) {
                $sawGap = true;
            }
        }

        if ($completePairs < SequenceRequest::MIN_RULES) {
            $this->context->buildViolation($constraint->tooFewMessage)
                ->setParameter('{{ min }}', (string) SequenceRequest::MIN_RULES)
                ->atPath('int'.min($completePairs + 1, SequenceRequest::MAX_RULES))
                ->addViolation();
        }
    }
}
