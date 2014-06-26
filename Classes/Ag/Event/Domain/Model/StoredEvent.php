<?php
namespace Ag\Event\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class StoredEvent {

	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="bigint", options={"unsigned"=true})
	 */
	protected $eventId;

	/**
	 * @var \DateTime
	 */
	protected $occuredOn;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $event;

	/**
	 * @param DomainEvent $event
	 */
	public function __construct(DomainEvent $event) {
		$this->occuredOn = clone $event->occuredOn;
		$this->event = serialize($event);
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
