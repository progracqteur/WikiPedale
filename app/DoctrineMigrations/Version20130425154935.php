<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130425154935 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        

        $this->addSql("CREATE SEQUENCE notification_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE notification_subscription (id INT NOT NULL, zone_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, group_id INT DEFAULT NULL, kind VARCHAR(20) NOT NULL, frequency INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_A2C88EE69F2C3FAB ON notification_subscription (zone_id)");
        $this->addSql("CREATE INDEX IDX_A2C88EE67E3C61F9 ON notification_subscription (owner_id)");
        $this->addSql("CREATE INDEX IDX_A2C88EE6FE54D947 ON notification_subscription (group_id)");
        $this->addSql("ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE69F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE6FE54D947 FOREIGN KEY (group_id) REFERENCES group_table (id) NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema)
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE notification_subscription_id_seq");

        $this->addSql("DROP TABLE notification_subscription");

    }
}
