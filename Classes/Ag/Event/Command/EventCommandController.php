<?php
namespace Ag\Event\Command;

use Ag\Event\Domain\Model\DomainEvent;
use Ag\Event\Domain\Model\StoredEvent;
use Ag\Event\Exception\EventHandlingException;
use Ag\Event\Service\EventService;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Flow\Log\SystemLoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class EventCommandController extends CommandController {

	/**
	 * The amount of job queries until the process quits and has to be respawned
	 */
	const LOOPS = 100;

	/**
	 * The job queue timeout
	 */
	const QUEUE_TIMEOUT = 20;

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
		$this->tellStatus('Event processor "%s" starting.', array($key));

		$i = 0;
		while($i < self::LOOPS) {
			$i++;
			$job = $this->pheanstalk
				  ->watch($key)
				  ->ignore('default')
				  ->reserve(self::QUEUE_TIMEOUT);

			if ($job instanceof Job) {
				$this->tellStatus('Found job #%d in tube "%s".', array($job->getId(), $key));

				/** @var $storedEvent StoredEvent */
				$storedEvent = unserialize($job->getData());

				$this->tellStatus('Attempt to handle event #%s.', array($storedEvent->getEventId()));

				$this->eventService->handleDomainEventByEventHandler($storedEvent->getEvent(), str_replace('_', '\\', $key));

				$this->pheanstalk->delete($job);
				$this->tellStatus('Deleted job #%d in tube "%s".', array($job->getId(), $key));
			}
		}

		if ($i >= self::LOOPS) {
			$this->tellStatus('Event processor reached %d loops. Will restart.', array(self::LOOPS));
		}

		$this->tellStatus('Event processor exited intentionally.');
	}

	/**
	 * @param string $message
	 * @param array $arguments
	 */
	protected function tellStatus($message, array $arguments = NULL) {
		$message = vsprintf($message, $arguments);
		$this->systemLogger->log($message, LOG_INFO);
		echo sprintf('%s: %s', date(\DateTime::ISO8601), $message) . PHP_EOL;
	}

}
