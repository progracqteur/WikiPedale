<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130221211353 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //create table PlaceType
        $this->addSql("CREATE TABLE PlaceType (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id));");
        
        //add column to place
        $this->addSql("alter table Place add column type_id INT DEFAULT NULL");
        
        //add index
        $this->addSql("CREATE INDEX IDX_741D53CDC54C8C93 ON place (type_id);");
        
        //add sequences
        $this->addSql("CREATE SEQUENCE PlaceType_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        
        //add link between foreign key
        $this->addSql("ALTER TABLE place ADD CONSTRAINT FK_741D53CDC54C8C93 FOREIGN KEY (type_id) REFERENCES PlaceType (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        
        
        

    }

    public function down(Schema $schema)
    {
        $this->addSql("alter table place drop column type_id");
        
        $this->addSql("drop table PlaceType CASCADE");
        
        $this->addSql("drop sequence PlaceType_id_seq CASCADE");

    }
}
