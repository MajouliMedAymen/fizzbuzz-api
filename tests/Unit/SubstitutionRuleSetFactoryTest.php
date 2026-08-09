<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\SequenceRequest;
use App\Factory\SubstitutionRuleSetFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubstitutionRuleSetFactory::class)]
final class SubstitutionRuleSetFactoryTest extends TestCase
{
    private SubstitutionRuleSetFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new SubstitutionRuleSetFactory();
    }

    public function testItBuildsOneRulePerProvidedPair(): void
    {
        $ruleSet = $this->factory->createFromRequest(
            new SequenceRequest(int1: 3, int2: 5, str1: 'fizz', str2: 'buzz', limit: 15),
        );

        self::assertCount(2, $ruleSet);
        self::assertSame('fizzbuzz', $ruleSet->findReplacementFor(15));
        self::assertNull($ruleSet->findReplacementFor(7));
    }
}
