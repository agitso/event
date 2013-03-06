<?php
namespace Ag\Event\Domain\Model;

abstract class DomainEvent {

	/**
	 * @var \DateTime
	 */
	protected $occuredOn;

	public function __construct() {
		$this->occuredOn = new \DateTime();
	}

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn() {
		return $this->occuredOn;
	}
}
?>