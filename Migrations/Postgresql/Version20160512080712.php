<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create sequence and table for StoredEvent domain model.
 */
class Version20160512080712 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql('CREATE SEQUENCE ag_event_domain_model_storedevent_eventid_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ag_event_domain_model_storedevent (eventid BIGINT NOT NULL, occuredon TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, event TEXT NOT NULL, PRIMARY KEY(eventid))');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql('DROP SEQUENCE ag_event_domain_model_storedevent_eventid_seq CASCADE');
        $this->addSql('DROP TABLE ag_event_domain_model_storedevent');
    }
}
