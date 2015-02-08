<?php
namespace Ag\Event\Bus;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Subscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EventBus {

	/**
	 * @var array<EventSubscriberInterface>
	 */
	protected $eventSubscribers = array();

	/**
	 * @var array<PublishingChannelInterface>
	 */
	protected $publishingChannels = array();

	/**
	 * @var array<DomainEvent>
	 */
	protected $scheduledEvents = array();

	/**
	 * Schedules an event for publishing to remove subscribers.
	 *
	 * @param DomainEvent $event
	 */
	public function schedule(DomainEvent $event) {
		$this->scheduledEvents[] = $event;
	}

	/**
	 * Dispatches the event to event subscribers.
	 *
	 * @param DomainEvent $event
	 */
	public function dispatch(DomainEvent $event) {
		/** @var EventSubscriberInterface $eventSubscriber */
		foreach ($this->eventSubscribers as $eventSubscriber) {
			if ($eventSubscriber->isSubscribedToEvent($event) === TRUE) {
				$eventSubscriber->handle($event);
			}
		}
	}

	public function postFlush(PostFlushEventArgs $eventArgs) {
		$events = $this->scheduledEvents;
		$this->scheduledEvents = array();

		foreach ($events as $event) {
			// todo: publish to remote subscribers
		}
	}

}
