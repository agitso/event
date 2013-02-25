<?php
namespace Ag\Event\Service;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EventService {

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $systemLogger;

	/**
	 * @var \Ag\Event\Domain\Repository\StoredEventRepository
	 * @Flow\Inject
	 */
	protected $storedEventRepository;

	/**
	 * @var \TYPO3\Flow\SignalSlot\Dispatcher
	 * @Flow\Inject
	 */
	protected $dispatcher;

	/**
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 */
	public function publish($event) {
		$event = new \Ag\Event\Domain\Model\StoredEvent($event);
		$this->storedEventRepository->add($event);
		$this->systemLogger->log('Published ' . get_class($event), LOG_DEBUG, serialize($event));
	}

	/**
	 * @return void
	 */
	public function processEvents() {
		$events = $this->storedEventRepository->getPersistedEvents();

		$this->systemLogger->log('Processing ' . count($events) . ' events.', LOG_DEBUG);

		foreach($events as $event) {
			$event = $event->getEvent();
			$this->systemLogger->log('Processing ' . get_class($event), LOG_DEBUG, serialize($event));

			$this->dispatcher->dispatch(get_class($this), get_class($event), array($event));
		}

		$this->storedEventRepository->resetPersistedEvents();
	}


}
?>