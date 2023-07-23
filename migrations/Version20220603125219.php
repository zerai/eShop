<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603125219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create|drop projections table';
    }

    public function up(Schema $schema): void
    {
//        $this->addSql('CREATE TABLE projections (
//              no BIGSERIAL,
//              name VARCHAR(150) NOT NULL,
//              position JSONB,
//              state JSONB,
//              status VARCHAR(28) NOT NULL,
//              locked_until CHAR(26),
//              PRIMARY KEY (no),
//              UNIQUE (name)
//            );
//        ');



        $this->addSql('CREATE SEQUENCE projections_no_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE projections (no BIGSERIAL NOT NULL, name VARCHAR(150) NOT NULL, "position" JSONB DEFAULT NULL, state JSONB DEFAULT NULL, status VARCHAR(28) NOT NULL, locked_until CHAR(26) DEFAULT NULL, PRIMARY KEY(no))');
        $this->addSql('CREATE UNIQUE INDEX projections_name_key ON projections (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE projections_no_seq CASCADE');
        $this->addSql('DROP TABLE projections');
    }
}
