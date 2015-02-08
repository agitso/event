<?php
namespace Ag\Event\Store;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Subscriber\EventSubscriberInterface;
use Ag\Event\Subscriber\Exception\NotSubscribedToEventException;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EventStoringSubscriber implements EventSubscriberInterface {

	/**
	 * @var array<EventStoreInterface>
	 */
	protected $eventStores;

	/**
	 * @param DomainEvent $event
	 * @return boolean TRUE if the Subscriber can handle the given event
	 */
	public function isSubscribedToEvent(DomainEvent $event) {
		return TRUE;
	}

	/**
	 * @return boolean
	 */
	public function isSynchronous() {
		return TRUE;
	}

	/**
	 * Handles the event.
	 *
	 * @param DomainEvent $event
	 * @return void
	 * @throws NotSubscribedToEventException If the event subscriber is not subscribed to this event
	 */
	public function handle(DomainEvent $event) {
		// TODO: Implement handle() method.
	}

}
