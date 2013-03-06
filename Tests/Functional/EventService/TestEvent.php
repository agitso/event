<?php
namespace Ag\Event\Tests\Functional\EventService;

class TestEvent extends \Ag\Event\Domain\Model\DomainEvent {

	/**
	 * @var string
	 */
	protected $message;

	public function __construct($message) {
		parent::__construct();
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}
}
?>