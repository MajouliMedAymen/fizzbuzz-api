<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RequestStatistic;
use App\Statistics\RequestFingerprint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestStatistic>
 */
class RequestStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestStatistic::class);
    }

    /**
     * @param array<string, int|string|null> $parameters
     */
    public function incrementHitCount(RequestFingerprint $fingerprint, array $parameters): void
    {
        $upsertHitCountSql = <<<'SQL'
            INSERT INTO request_statistic
                (fingerprint, int1, int2, request_limit, str1, str2, hits, last_hit_at)
            VALUES
                (:fingerprint, :int1, :int2, :limit, :str1, :str2, 1, :now)
            ON CONFLICT (fingerprint) DO UPDATE
                SET hits = request_statistic.hits + 1,
                    last_hit_at = EXCLUDED.last_hit_at
            SQL;

        $this->getEntityManager()->getConnection()->executeStatement($upsertHitCountSql, [
            'fingerprint' => $fingerprint->toString(),
            'int1' => (int) $parameters['int1'],
            'int2' => (int) $parameters['int2'],
            'limit' => (int) $parameters['limit'],
            'str1' => (string) $parameters['str1'],
            'str2' => (string) $parameters['str2'],
            'now' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ], [
            'int1' => ParameterType::INTEGER,
            'int2' => ParameterType::INTEGER,
            'limit' => ParameterType::INTEGER,
        ]);
    }

    public function findMostFrequentlyRequested(): ?RequestStatistic
    {
        return $this->createQueryBuilder('statistic')
            ->orderBy('statistic.hitCount', 'DESC')
            ->addOrderBy('statistic.lastHitAt', 'DESC')
            ->addOrderBy('statistic.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
