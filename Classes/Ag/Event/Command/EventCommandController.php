<?php
namespace Ag\Event\Command;

use Ag\Event\Domain\Model\StoredEvent;
use Ag\Event\Domain\Repository\StoredEventRepository;
use Ag\Event\Exception\StoredEventNotFoundException;
use Ag\Event\Service\EventService;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\Flow\Core\Booting\Scripts;
use TYPO3\Flow\Log\SystemLoggerInterface;

/**
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
	 * Populated via Objects.yaml.
	 *
	 * @var array
	 */
	protected $subProcessCommandSettings;

	/**
	 * @Flow\Inject
	 * @var EventService
	 */
	protected $eventService;

	/**
	 * @Flow\Inject
	 * @var StoredEventRepository
	 */
	protected $storedEventRepository;

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
				Scripts::executeCommand(
					'ag.event:event:handle',
					$this->subProcessCommandSettings,
					TRUE,
					array(
						'eventId' => $storedEvent->getEventId(),
						'eventHandler' => $key
					)
				);

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
	 * @param string $eventId The Event ID to handle
	 * @param string $eventHandler The event handler as identifier, e.g. Acme_Notification_Domain_EventHandler_ProductBackInStock
	 * @throws StoredEventNotFoundException
	 */
	public function handleCommand($eventId, $eventHandler ) {
		$storedEvent = $this->storedEventRepository->findByIdentifier($eventId);
		if ($storedEvent === NULL) {
			throw new StoredEventNotFoundException(sprintf('The Domain Event with ID "%s" was not found.', $eventId), 1422956394);
		}

		$this->tellStatus('Attempt to handle event #%s with handler "%s".', array($storedEvent->getEventId(), $eventHandler));
		$this->eventService->handleDomainEventByEventHandler($storedEvent->getEvent(), str_replace('_', '\\', $eventHandler));

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
