<?php
namespace Ag\Event\Bus;

use Ag\Event\Logging\EventLoggerInterface;
use Ag\Event\Domain\Model\DomainEvent;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EventPublisher {

	/**
	 * @var EventLoggerInterface
	 * @Flow\Inject
	 */
	protected $eventLogger;

	/**
	 * @var EventBus
	 * @Flow\Inject
	 */
	protected $eventBus;

	/**
	 * Publishes the event into the messaging backends.
	 *
	 * @param DomainEvent $event
	 */
	public function publish(DomainEvent $event) {
		$this->eventBus->schedule($event);
	}

}


