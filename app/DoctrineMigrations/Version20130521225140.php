<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;


/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130521225140 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        

        $this->addSql("ALTER TABLE notification_subscription ADD place_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE notification_subscription ADD transporter VARCHAR(20) NOT NULL default '".NotificationSubscription::TRANSPORTER_MAIL."'");
        $this->addSql("ALTER TABLE notification_subscription ADD groupRef_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE6FC0F8364 FOREIGN KEY (groupRef_id) REFERENCES group_table (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification_subscription ADD CONSTRAINT FK_A2C88EE6DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_A2C88EE6FC0F8364 ON notification_subscription (groupRef_id)");
        $this->addSql("CREATE INDEX IDX_A2C88EE6DA6A219 ON notification_subscription (place_id)");
        $this->addSql("UPDATE notification_subscription set groupRef_id = group_id where kind ='".NotificationSubscription::KIND_MANAGER."'");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        

        $this->addSql("ALTER TABLE notification_subscription DROP place_id");
        $this->addSql("ALTER TABLE notification_subscription DROP transporter");
        $this->addSql("ALTER TABLE notification_subscription DROP groupRef_id");
        //$this->addSql("ALTER TABLE notification_subscription DROP CONSTRAINT fk_a2c88ee6fc0f8364");
        $this->addSql("ALTER TABLE notification_subscription DROP CONSTRAINT fk_a2c88ee6fe54d947");
        //$this->addSql("DROP INDEX IDX_A2C88EE6FC0F8364");
        //$this->addSql("DROP INDEX IDX_A2C88EE6DA6A219");
    }
}
