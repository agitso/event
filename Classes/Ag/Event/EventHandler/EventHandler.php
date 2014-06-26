<?php
namespace Ag\Event\EventHandler;

use Ag\Event\Domain\Model\DomainEvent;

/**
 */
interface EventHandler {

	/**
	 * @param DomainEvent $event
	 * @return void
	 */
	public function handle(DomainEvent $event);

}
