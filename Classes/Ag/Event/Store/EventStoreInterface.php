<?php
namespace Ag\Event\Store;

use Ag\Event\Domain\Model\DomainEvent;

interface EventStoreInterface {

	/**
	 * Stores the given event.
	 *
	 * @param DomainEvent $event
	 * @return void
	 */
	public function store(DomainEvent $event);

}