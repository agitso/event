<?php
namespace Ag\Event\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class StoredEventRepository extends \TYPO3\Flow\Persistence\Repository {


	/**
	 * @var array
	 */
	protected $persistedEvents = array();

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $storedEvent
	 * @return void
	 */
	public function add($storedEvent) {
		parent::add($storedEvent);

		$this->persistedEvents[] = $storedEvent;
	}

	/**
	 * @return void
	 */
	public function resetPersistedEvents() {
		$this->persistedEvents = array();
	}

	/**
	 * @return array
	 */
	public function getPersistedEvents() {
		return $this->persistedEvents;
	}
}
?>