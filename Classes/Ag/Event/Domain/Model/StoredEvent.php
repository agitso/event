<?php
namespace Ag\Event\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class StoredEvent {

	/**
	 * @var int
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
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 */
	public function __construct($event) {
		$this->occuredOn = new \DateTime();
		$this->event = serialize($event);
	}

	/**
	 * @return \Ag\Event\Domain\Model\DomainEvent
	 */
	public function getEvent() {
		return unserialize($this->event);
	}
}
?>