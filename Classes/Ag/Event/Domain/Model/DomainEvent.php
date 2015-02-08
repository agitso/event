<?php
namespace Ag\Event\Domain\Model;

use TYPO3\Flow\Utility\Algorithms;

abstract class DomainEvent {

	/**
	 * @var string
	 */
	public $eventId;

	/**
	 * @var \DateTime
	 */
	public $occurredOn;

	public function __construct() {
		$this->eventId = Algorithms::generateUUID();
		$this->occuredOn = new \DateTime();
	}

}
