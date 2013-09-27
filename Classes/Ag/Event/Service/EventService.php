<?php
namespace Ag\Event\Service;

use TYPO3\Flow\Annotations as Flow;

require_once(FLOW_PATH_PACKAGES . '/Libraries/pda/pheanstalk/pheanstalk_init.php');

/**
 * @Flow\Scope("singleton")
 */
class EventService {

	/**
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $entityManager
	 * @return void
	 */
	public function injectEntityManager(\Doctrine\Common\Persistence\ObjectManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->entityManager->getEventManager()->addEventListener(array(\Doctrine\ORM\Events::postFlush), $this);
	}

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @var array
	 */
	protected $events = array();

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

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
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $systemLogger;

	/**
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	protected $logging = FALSE;

	/**
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 */
	public function publish($event) {
		if($this->logging) {
			$this->systemLogger->log('Publish event ' . get_class($event), LOG_DEBUG);
		}
		$event = new \Ag\Event\Domain\Model\StoredEvent($event);
		$this->storedEventRepository->add($event);
		$this->events[] = $event;
	}

	/**
	 * @param \Doctrine\ORM\Event\PostFlushEventArgs $eventArgs
	 * @return void
	 */
	public function postFlush(\Doctrine\ORM\Event\PostFlushEventArgs $eventArgs) {
		$events = $this->events;
		$this->events = array();

		foreach ($events as $event) {
			foreach ($this->getAsyncEventHandlers() as $eventHandler) {
				$this->_asyncPublish($event, $eventHandler);
			}

			foreach ($this->getSyncEventHandlers() as $eventHandler) {
				$this->_syncPublish($event, $eventHandler);
			}
		}
	}

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 * @param string $eventHandler
	 */
	public function _syncPublish($event, $eventHandler) {
		if($this->logging) {
			$this->systemLogger->log('Syncronously publishing event #' . $event->getEventId() . ' to ' . $eventHandler, LOG_DEBUG);
		}

		$eventHandlerInstance = $this->objectManager->get($eventHandler);

		if(!$eventHandlerInstance instanceof \Ag\Event\EventHandler\EventHandler) {
			$this->systemLogger->log('Event handler ' . $eventHandler . ' does not implement the event handler interface.', LOG_CRIT);
			return;
		}

		try {
			$eventHandlerInstance->handle($event->getEvent());
		} catch(\Exception $e) {
			$this->systemLogger->log('Event #' . $event->getEventId() . 'could not be handled.', LOG_CRIT, array(
				'event'=>serialize($event->getEvent()),
				'exception'=>$e->getMessage(),
				'trace'=>$e->getTrace()
			));
		}
	}

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 * @param string $key
	 */
	protected function _asyncPublish($event, $key) {
		$key = str_replace('\\', '_', $key);
		if($this->logging) {
			$this->systemLogger->log('Asyncronously publishing event #' . $event->getEventId() . ' to tube ' . $key, LOG_DEBUG);
		}
		$pheanstalk = new \Pheanstalk_Pheanstalk('127.0.0.1');
		$pheanstalk->useTube($key)->put(serialize($event));
	}

	/**
	 * @return array
	 */
	protected function getSyncEventHandlers() {
		$eventHandlers = $this->getEventHandlers();
		if (array_key_exists('sync', $eventHandlers) && is_array($eventHandlers['sync'])) {
			return $eventHandlers['sync'];
		} else {
			return array();
		}
	}

	/**
	 * @return array
	 */
	protected function getAsyncEventHandlers() {
		$eventHandlers = $this->getEventHandlers();
		if (array_key_exists('async', $eventHandlers) && is_array($eventHandlers['async'])) {
			return $eventHandlers['async'];
		} else {
			return array();
		}
	}

	/**
	 * @return array
	 */
	protected function getEventHandlers() {
		if (array_key_exists('eventHandlers', $this->settings) && is_array($this->settings['eventHandlers'])) {
			return $this->settings['eventHandlers'];
		} else {
			return array();
		}
	}
}

?>