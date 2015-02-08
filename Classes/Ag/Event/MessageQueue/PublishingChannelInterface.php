<?php
namespace Ag\Event\MessageQueue;

use Ag\Event\Domain\Model\DomainEvent;

interface PublishingChannelInterface {

	/**
	 * @param DomainEvent $event
	 * @return void
	 */
	public function publish(DomainEvent $event);

}
