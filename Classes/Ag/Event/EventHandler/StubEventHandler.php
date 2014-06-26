<?php
namespace Ag\Event\EventHandler;

use Ag\Event\Domain\Model\DomainEvent;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class StubEventHandler implements EventHandler {

	/**
	 * @var array
	 */
	public $events = array();

	/**
	 * @param DomainEvent $event
	 * @return void
	 */
	public function handle(DomainEvent $event) {
		$this->events[] = $event;
	}

}
