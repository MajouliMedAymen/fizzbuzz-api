<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\SequenceGenerator;
use App\Domain\SubstitutionRule;
use App\Domain\SubstitutionRuleSet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SequenceGenerator::class)]
#[CoversClass(SubstitutionRuleSet::class)]
#[CoversClass(SubstitutionRule::class)]
final class SequenceGeneratorTest extends TestCase
{
    private SequenceGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new SequenceGenerator();
    }

    public function testClassicFizzBuzzFirstFifteenValues(): void
    {
        $sequence = $this->generateWithTwoRules(3, 5, 15, 'fizz', 'buzz');

        self::assertSame(
            ['1', '2', 'fizz', '4', 'buzz', 'fizz', '7', '8', 'fizz', 'buzz', '11', 'fizz', '13', '14', 'fizzbuzz'],
            $sequence,
        );
    }

    /**
     * @return list<string>
     */
    private function generateWithTwoRules(
        int $firstDivisor,
        int $secondDivisor,
        int $limit,
        string $firstText,
        string $secondText,
    ): array {
        $ruleSet = new SubstitutionRuleSet(
            new SubstitutionRule($firstDivisor, $firstText),
            new SubstitutionRule($secondDivisor, $secondText),
        );

        return $this->generator->generateAll($ruleSet, $limit);
    }
}
