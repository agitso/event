<?php
namespace Ag\Event\Command;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Service\EventService;
use Pheanstalk\Pheanstalk;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Flow\Log\SystemLoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class EventCommandController extends CommandController {

	/**
	 * @Flow\Inject
	 * @var EventService
	 */
	protected $eventService;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * Note: this dependency injection is backed by Objects.yaml!
	 *
	 * @Flow\Inject
	 * @var Pheanstalk
	 */
	protected $pheanstalk;

	/**
	 * @param string $key
	 */
	public function processCommand($key) {
		while(TRUE) {
			$this->systemLogger->log('Waiting for event in tube ' . $key, LOG_DEBUG);
			$job = $this->pheanstalk
				  ->watch($key)
				  ->ignore('default')
				  ->reserve();

			/** @var $domainEvent DomainEvent */
			$domainEvent = unserialize($job->getData());
			$this->eventService->handleDomainEventByEventHandler($domainEvent, str_replace('_', '\\', $key));

			$this->pheanstalk->delete($job);
		}
	}

}
