<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130429131410 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        

        $this->addSql("CREATE SEQUENCE PendingNotification_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE PendingNotification (id INT NOT NULL, subscription_id INT DEFAULT NULL, placeTracking_id INT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_84F8E61C9A1887DC ON PendingNotification (subscription_id)");
        $this->addSql("CREATE INDEX IDX_84F8E61CB6124219 ON PendingNotification (placeTracking_id)");
        $this->addSql("ALTER TABLE PendingNotification ADD CONSTRAINT FK_84F8E61C9A1887DC FOREIGN KEY (subscription_id) REFERENCES notification_subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE PendingNotification ADD CONSTRAINT FK_84F8E61CB6124219 FOREIGN KEY (placeTracking_id) REFERENCES placeTracking (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE PendingNotification_id_seq");

        $this->addSql("DROP TABLE PendingNotification");

    }
}
