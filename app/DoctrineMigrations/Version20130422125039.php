<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130422125039 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE placeTracking_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE photos_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE comments_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE placetype_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE place_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE group_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE zones (id INT NOT NULL, name VARCHAR(60) NOT NULL, slug VARCHAR(60) NOT NULL, codeProvince VARCHAR(4) NOT NULL, polygon geography(POLYGON,4326) NOT NULL, center geography(POINT,4326) NOT NULL, type VARCHAR(5) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX slug ON zones (slug)");
        $this->addSql("COMMENT ON COLUMN zones.polygon IS '(DC2Type:polygon)'");
        $this->addSql("COMMENT ON COLUMN zones.center IS '(DC2Type:point)'");
        $this->addSql("CREATE TABLE placeTracking (id INT NOT NULL, author_id INT DEFAULT NULL, place_id INT DEFAULT NULL, isCreation BOOLEAN NOT NULL, details xml NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_118EF6CCF675F31B ON placeTracking (author_id)");
        $this->addSql("CREATE INDEX IDX_118EF6CCDA6A219 ON placeTracking (place_id)");
        $this->addSql("COMMENT ON COLUMN placeTracking.details IS '(DC2Type:hash)'");
        $this->addSql("CREATE TABLE photos (id INT NOT NULL, creator_id INT DEFAULT NULL, place_id INT DEFAULT NULL, file VARCHAR(255) NOT NULL, height INT NOT NULL, width INT NOT NULL, createDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, legend VARCHAR(500) NOT NULL, published BOOLEAN NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_876E0D961220EA6 ON photos (creator_id)");
        $this->addSql("CREATE INDEX IDX_876E0D9DA6A219 ON photos (place_id)");
        $this->addSql("CREATE INDEX photo_file ON photos (file)");
        $this->addSql("CREATE TABLE categories (id INT NOT NULL, parent_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)");
        $this->addSql("CREATE TABLE comments (id INT NOT NULL, creator_id INT DEFAULT NULL, place_id INT DEFAULT NULL, content TEXT NOT NULL, published BOOLEAN NOT NULL, creationDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, kind VARCHAR(10) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_5F9E962A61220EA6 ON comments (creator_id)");
        $this->addSql("CREATE INDEX IDX_5F9E962ADA6A219 ON comments (place_id)");
        $this->addSql("CREATE TABLE placetype (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE notations (id VARCHAR(20) NOT NULL, name VARCHAR(60) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE place (id INT NOT NULL, creator_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, type_id INT DEFAULT NULL, geom geography(POINT,4326) NOT NULL, description TEXT NOT NULL, createDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nbVote INT NOT NULL, nbComm INT NOT NULL, infos xml NOT NULL, nbPhoto INT NOT NULL, address xml NOT NULL, lastUpdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, accepted BOOLEAN NOT NULL, moderatorComment TEXT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_741D53CD61220EA6 ON place (creator_id)");
        $this->addSql("CREATE INDEX IDX_741D53CD783E3463 ON place (manager_id)");
        $this->addSql("CREATE INDEX IDX_741D53CDC54C8C93 ON place (type_id)");
        $this->addSql("COMMENT ON COLUMN place.geom IS '(DC2Type:point)'");
        $this->addSql("COMMENT ON COLUMN place.infos IS '(DC2Type:hash)'");
        $this->addSql("COMMENT ON COLUMN place.address IS '(DC2Type:address)'");
        $this->addSql("CREATE TABLE place_category (place_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(place_id, category_id))");
        $this->addSql("CREATE INDEX IDX_2C17FE42DA6A219 ON place_category (place_id)");
        $this->addSql("CREATE INDEX IDX_2C17FE4212469DE2 ON place_category (category_id)");
        $this->addSql("CREATE TABLE group_table (id INT NOT NULL, notation_id VARCHAR(20) DEFAULT NULL, zone_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, roles TEXT NOT NULL, gtype VARCHAR(12) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_A605A4215E237E06 ON group_table (name)");
        $this->addSql("CREATE INDEX IDX_A605A4219680B7F7 ON group_table (notation_id)");
        $this->addSql("CREATE INDEX IDX_A605A4219F2C3FAB ON group_table (zone_id)");
        $this->addSql("COMMENT ON COLUMN group_table.roles IS '(DC2Type:array)'");
        $this->addSql("CREATE TABLE users (id INT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locked BOOLEAN NOT NULL, expired BOOLEAN NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, credentials_expired BOOLEAN NOT NULL, credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creationDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, infos xml NOT NULL, nbComment INT NOT NULL, nbVote INT NOT NULL, phonenumber VARCHAR(50) NOT NULL, fullLabel VARCHAR(150) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)");
        $this->addSql("COMMENT ON COLUMN users.roles IS '(DC2Type:array)'");
        $this->addSql("COMMENT ON COLUMN users.infos IS '(DC2Type:hash)'");
        $this->addSql("CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(user_id, group_id))");
        $this->addSql("CREATE INDEX IDX_8F02BF9DA76ED395 ON user_group (user_id)");
        $this->addSql("CREATE INDEX IDX_8F02BF9DFE54D947 ON user_group (group_id)");
        $this->addSql("ALTER TABLE placeTracking ADD CONSTRAINT FK_118EF6CCF675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE placeTracking ADD CONSTRAINT FK_118EF6CCDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE photos ADD CONSTRAINT FK_876E0D961220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE photos ADD CONSTRAINT FK_876E0D9DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE comments ADD CONSTRAINT FK_5F9E962ADA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE place ADD CONSTRAINT FK_741D53CD61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE place ADD CONSTRAINT FK_741D53CD783E3463 FOREIGN KEY (manager_id) REFERENCES group_table (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE place ADD CONSTRAINT FK_741D53CDC54C8C93 FOREIGN KEY (type_id) REFERENCES placetype (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE42DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE4212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE group_table ADD CONSTRAINT FK_A605A4219680B7F7 FOREIGN KEY (notation_id) REFERENCES notations (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE group_table ADD CONSTRAINT FK_A605A4219F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES group_table (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE group_table DROP CONSTRAINT FK_A605A4219F2C3FAB");
        $this->addSql("ALTER TABLE categories DROP CONSTRAINT FK_3AF34668727ACA70");
        $this->addSql("ALTER TABLE place_category DROP CONSTRAINT FK_2C17FE4212469DE2");
        $this->addSql("ALTER TABLE place DROP CONSTRAINT FK_741D53CDC54C8C93");
        $this->addSql("ALTER TABLE group_table DROP CONSTRAINT FK_A605A4219680B7F7");
        $this->addSql("ALTER TABLE placeTracking DROP CONSTRAINT FK_118EF6CCDA6A219");
        $this->addSql("ALTER TABLE photos DROP CONSTRAINT FK_876E0D9DA6A219");
        $this->addSql("ALTER TABLE comments DROP CONSTRAINT FK_5F9E962ADA6A219");
        $this->addSql("ALTER TABLE place_category DROP CONSTRAINT FK_2C17FE42DA6A219");
        $this->addSql("ALTER TABLE place DROP CONSTRAINT FK_741D53CD783E3463");
        $this->addSql("ALTER TABLE user_group DROP CONSTRAINT FK_8F02BF9DFE54D947");
        $this->addSql("ALTER TABLE placeTracking DROP CONSTRAINT FK_118EF6CCF675F31B");
        $this->addSql("ALTER TABLE photos DROP CONSTRAINT FK_876E0D961220EA6");
        $this->addSql("ALTER TABLE comments DROP CONSTRAINT FK_5F9E962A61220EA6");
        $this->addSql("ALTER TABLE place DROP CONSTRAINT FK_741D53CD61220EA6");
        $this->addSql("ALTER TABLE user_group DROP CONSTRAINT FK_8F02BF9DA76ED395");
        $this->addSql("DROP SEQUENCE placeTracking_id_seq");
        $this->addSql("DROP SEQUENCE photos_id_seq");
        $this->addSql("DROP SEQUENCE categories_id_seq");
        $this->addSql("DROP SEQUENCE comments_id_seq");
        $this->addSql("DROP SEQUENCE placetype_id_seq");
        $this->addSql("DROP SEQUENCE place_id_seq");
        $this->addSql("DROP SEQUENCE group_table_id_seq");
        $this->addSql("DROP SEQUENCE users_id_seq");
        $this->addSql("DROP TABLE zones");
        $this->addSql("DROP TABLE placeTracking");
        $this->addSql("DROP TABLE photos");
        $this->addSql("DROP TABLE categories");
        $this->addSql("DROP TABLE comments");
        $this->addSql("DROP TABLE placetype");
        $this->addSql("DROP TABLE notations");
        $this->addSql("DROP TABLE place");
        $this->addSql("DROP TABLE place_category");
        $this->addSql("DROP TABLE group_table");
        $this->addSql("DROP TABLE users");
        $this->addSql("DROP TABLE user_group");
    }
}
