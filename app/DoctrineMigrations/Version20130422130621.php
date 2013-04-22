<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130422130621 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE INDEX cities_geog ON zones USING GIST ( polygon ); ");
        $this->addSql("CREATE INDEX place_geog ON place USING GIST ( geom );");

    }

    public function down(Schema $schema)
    {
        $this->addSql("Drop INDEX cities_geog");
        $this->addSql("Drop Index place_geog");

    }
}
