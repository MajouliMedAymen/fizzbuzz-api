<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE request_statistic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<'SQL'
            CREATE TABLE request_statistic (
                id INT NOT NULL,
                fingerprint VARCHAR(64) NOT NULL,
                int1 INT NOT NULL,
                int2 INT NOT NULL,
                request_limit INT NOT NULL,
                str1 VARCHAR(64) NOT NULL,
                str2 VARCHAR(64) NOT NULL,
                hits BIGINT DEFAULT 1 NOT NULL,
                last_hit_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )
            SQL);
        $this->addSql('ALTER TABLE request_statistic ALTER id SET DEFAULT nextval(\'request_statistic_id_seq\')');
        $this->addSql('CREATE UNIQUE INDEX uniq_request_fingerprint ON request_statistic (fingerprint)');
        $this->addSql('CREATE INDEX idx_request_hits ON request_statistic (hits DESC)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE request_statistic');
        $this->addSql('DROP SEQUENCE request_statistic_id_seq');
    }
}
