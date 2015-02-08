<?php
namespace Ag\Event\Store\PersistenceStore;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\QueryInterface;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class StoredEventRepository extends Repository {

	const ENTITY_CLASSNAME = 'Ag\\Event\\Store\\MysqlStore\\StoredEvent';

	/**
	 * @param string $eventId
	 * @return NULL|StoredEvent
	 */
	public function findByIdentifier($eventId) {
		return $this->findOneByEventId($eventId);
	}

	/**
	 * @return NULL|\Ag\Event\Store\PersistenceStore\Domain\Model\\Ag\Event\Store\MysqlStore\StoredEvent
	 * @todo This function is not used
	 */
	public function getLatestEvent() {
		$query = $this->createQuery();
		return $query
			->setOrderings(array('eventStoreId'=> QueryInterface::ORDER_DESCENDING))
			->setLimit(1)
			->execute()
			->getFirst();
	}

	/**
	 * @param NULL|\Ag\Event\Store\MysqlStore\Domain\Model\\Ag\Event\Store\MysqlStore\StoredEvent $event
	 * @return NULL|\Ag\Event\Store\PersistenceStore\Domain\Model\\Ag\Event\Store\MysqlStore\StoredEvent
	 * @todo This function is not used
	 */
	public function findNext($event) {
		$eventId = $event !== NULL ? $event->getEventStoreId() : '0';

		$query = $this->createQuery();
		return $query
			->matching($query->greaterThan('eventStoreId', $eventId))
			->setOrderings(array('eventStoreId'=> QueryInterface::ORDER_ASCENDING))
			->setLimit(1)
			->execute()
			->getFirst();
	}

}
