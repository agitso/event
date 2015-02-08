<?php
namespace Ag\Event\Store\PersistenceStore;

use Ag\Event\Domain\Model\DomainEvent;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class StoredEvent {

	/**
	 * This is mainly for sorting. Events are primarily identified by their eventId.
	 *
	 * @var string
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="bigint", options={"unsigned"=true})
	 */
	protected $eventStoreId;

	/**
	 * @var string
	 * @ORM\Column(unique=true)
	 */
	protected $eventId;

	/**
	 * Event class name
	 *
	 * @var string
	 */
	protected $eventType;

	/**
	 * @var \DateTime
	 */
	protected $occurredOn;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $event;

	/**
	 * @param DomainEvent $event
	 */
	public function __construct(DomainEvent $event) {
		$this->eventId = $event->eventId;
		$this->eventType = get_class($event);
		$this->event = serialize($event);
		$this->occurredOn = clone $event->occurredOn;
	}

	/**
	 * @return string
	 */
	public function getEventStoreId() {
		return $this->eventStoreId;
	}

	/**
	 * @return string
	 */
	public function getEventId() {
		return $this->eventId;
	}

	/**
	 * @return DomainEvent
	 */
	public function getEvent() {
		return unserialize($this->event);
	}

}
