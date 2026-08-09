<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RequestStatisticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestStatisticRepository::class)]
#[ORM\Table(name: 'request_statistic')]
#[ORM\UniqueConstraint(name: 'uniq_request_fingerprint', columns: ['fingerprint'])]
#[ORM\Index(name: 'idx_request_hits', columns: ['hits'])]
class RequestStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $fingerprint;

    #[ORM\Column(name: 'int1', type: Types::INTEGER)]
    private int $firstDivisor;

    #[ORM\Column(name: 'int2', type: Types::INTEGER)]
    private int $secondDivisor;

    #[ORM\Column(name: 'request_limit', type: Types::INTEGER)]
    private int $sequenceLimit;

    #[ORM\Column(name: 'str1', type: Types::STRING, length: 64)]
    private string $firstReplacementText;

    #[ORM\Column(name: 'str2', type: Types::STRING, length: 64)]
    private string $secondReplacementText;

    #[ORM\Column(name: 'hits', type: Types::BIGINT, options: ['default' => 1])]
    private string $hitCount = '1';

    #[ORM\Column(name: 'last_hit_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $lastHitAt;

    public function __construct(
        string $fingerprint,
        int $firstDivisor,
        int $secondDivisor,
        int $sequenceLimit,
        string $firstReplacementText,
        string $secondReplacementText,
    ) {
        $this->fingerprint = $fingerprint;
        $this->firstDivisor = $firstDivisor;
        $this->secondDivisor = $secondDivisor;
        $this->sequenceLimit = $sequenceLimit;
        $this->firstReplacementText = $firstReplacementText;
        $this->secondReplacementText = $secondReplacementText;
        $this->lastHitAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFingerprint(): string
    {
        return $this->fingerprint;
    }

    public function getFirstDivisor(): int
    {
        return $this->firstDivisor;
    }

    public function getSecondDivisor(): int
    {
        return $this->secondDivisor;
    }

    public function getSequenceLimit(): int
    {
        return $this->sequenceLimit;
    }

    public function getFirstReplacementText(): string
    {
        return $this->firstReplacementText;
    }

    public function getSecondReplacementText(): string
    {
        return $this->secondReplacementText;
    }

    public function getHitCount(): int
    {
        return (int) $this->hitCount;
    }

    public function getLastHitAt(): \DateTimeImmutable
    {
        return $this->lastHitAt;
    }
}
