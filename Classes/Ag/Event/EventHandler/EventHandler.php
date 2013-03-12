<?php
namespace Ag\Event\EventHandler;

interface EventHandler {

	/**
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 * @return void
	 */
	public function handle(\Ag\Event\Domain\Model\DomainEvent $event);

}
?>