<?php
namespace Ag\Event\Tests\Functional\EventService;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EventListener {

	/**
	 * @var array
	 */
	public $events;

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 */
	public function listen($event) {
		$this->events[] = $event;
	}
}
?>