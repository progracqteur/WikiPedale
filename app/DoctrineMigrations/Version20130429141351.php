<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130429141351 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("create OR replace function add_pending_notification() RETURNS trigger AS \$pending_notification$
    BEGIN
	-- add a pending notification to each new placetracking
	-- inside the bounding box of notification_subscription

	insert into pendingnotification (id, subscription_id, placetracking_id)
	    select nextval('pendingnotification_id_seq'),  notification_subscription.id, NEW.id
			from notification_subscription 
			join 
			( select zones.id as zone_id 
				from zones 
				join
					(select zones.id as zone_id, place.id as place_id from zones, place 
						where ST_Covers(zones.polygon, place.geom) 
						AND place.id = NEW.place_id
					) as subZone 
					on subZone.zone_id = zones.id
			) as subQ 
			on notification_subscription.zone_id = subQ.zone_id;
        RETURN NULL; -- result is ignored since this is an AFTER trigger
    END;
\$pending_notification$ LANGUAGE plpgsql;");
        $this->addSql("create trigger add_pending_notification 
AFTER INSERT on placetracking
FOR EACH ROW EXECUTE PROCEDURE add_pending_notification()");

    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("drop trigger add_pending_notification on placetracking");
        
        $this->addSql("drop function add_pending_notification()");

    }
}
