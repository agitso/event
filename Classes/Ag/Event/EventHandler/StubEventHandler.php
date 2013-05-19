<?php
namespace Ag\Event\EventHandler;

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
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 * @return void
	 */
	public function handle(\Ag\Event\Domain\Model\DomainEvent $event) {
		$this->events[] = $event;
	}

}
?>