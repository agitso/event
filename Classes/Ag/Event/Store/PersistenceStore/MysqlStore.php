<?php
namespace Ag\Event\Store\PersistenceStore;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Store\PersistenceStore\StoredEventRepository;
use Ag\Event\Store\EventStoreInterface;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class MysqlStore implements EventStoreInterface {

	/**
	 * @var StoredEventRepository
	 * @Flow\Inject
	 */
	protected $storedEventRepository;

	/**
	 * Stores the given event.
	 *
	 * @param DomainEvent $event
	 * @return void
	 */
	public function store(DomainEvent $event) {
		// TODO: Implement store() method.
	}

}
