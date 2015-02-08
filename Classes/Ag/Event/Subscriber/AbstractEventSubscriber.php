<?php
namespace Ag\Event\Subscriber;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Subscriber\Exception\MissingImplementationException;
use Ag\Event\Subscriber\Exception\NotSubscribedToEventException;

abstract class AbstractEventSubscriber implements EventSubscriberInterface {

	/**
	 * List of Domain Event class names (FQ) this event subscriber is subscribed to.
	 *
	 * @var array<string>
	 */
	protected $subscribedToEvents = array();

	/**
	 * @param DomainEvent $event
	 * @return boolean TRUE if the Subscriber can handle the given event
	 */
	public function isSubscribedToEvent(DomainEvent $event) {
		return in_array(get_class($event), $this->subscribedToEvents);
	}

	/**
	 * Handles the event by calling handleXYEvent on itself.
	 * E. g.: handle(Some\Domain\Event\ThingHappenedEvent) would result in a handleThingHappenedEvent() call.
	 *
	 * @param DomainEvent $event
	 * @throws MissingImplementationException
	 * @throws NotSubscribedToEventException If the event subscriber is not subscribed to this event
	 */
	public function handle(DomainEvent $event) {
		$eventName = $this->getEventNameFromEvent($event);
		$eventHandlingFunctionName = $this->getEventHandlingMethodNameFromEventName($eventName);

		// todo: fix these messages
		if ($this->isSubscribedToEvent($event) === FALSE) {
			throw new NotSubscribedToEventException('The event "' . $eventName . '" (' . get_class($event) . ') cannot be handled by this event subscriber because it\'s not registered to it.', 1423408453);
		}

		// todo: fix these messages
		if (method_exists($this, $eventHandlingFunctionName) === FALSE) {
			throw new MissingImplementationException('This event subscriber is subscribed to the event "' . $eventName . '" but is missing the implementation "' . $eventHandlingFunctionName . '" to handle the event.', 1423408454);
		}

		$this->$eventHandlingFunctionName($event);
	}

	/**
	 * @param DomainEvent $event
	 * @return string
	 */
	protected function getEventNameFromEvent(DomainEvent $event) {
		$className = get_class($event);
		$classNameParts = explode('\\', $className);
		return array_pop($classNameParts);
	}

	/**
	 * @param string $eventName
	 * @return string
	 */
	protected function getEventHandlingMethodNameFromEventName($eventName) {
		if (substr($eventName, -5, 5) !== 'Event') {
			$eventName .= 'Event';
		}

		return 'handle' . ucfirst($eventName);
	}

}
