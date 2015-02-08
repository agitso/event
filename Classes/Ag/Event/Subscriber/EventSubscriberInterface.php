<?php
namespace Ag\Event\Subscriber;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Subscriber\Exception\NotSubscribedToEventException;

interface EventSubscriberInterface {

	/**
	 * @param DomainEvent $event
	 * @return boolean TRUE if the Subscriber can handle the given event
	 */
	public function isSubscribedToEvent(DomainEvent $event);

	/**
	 * @return boolean
	 */
	public function isSynchronous();

	/**
	 * Handles the event.
	 *
	 * @todo This is already part of the error handling
	 *
	 * @param DomainEvent $event
	 * @return void
	 * @throws NotSubscribedToEventException If the event subscriber is not subscribed to this event
	 */
	public function handle(DomainEvent $event);

}
