<?php
namespace Ag\Event\Domain\Model;

abstract class DomainEvent {

	/**
	 * @var \DateTime
	 */
	public $occuredOn;

	public function __construct() {
		$this->occuredOn = new \DateTime();
	}
}
?>