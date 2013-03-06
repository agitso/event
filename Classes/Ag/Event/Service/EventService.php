<?php
namespace Ag\Event\Service;

use TYPO3\Flow\Annotations as Flow;

require_once(FLOW_PATH_PACKAGES . '/Libraries/pda/pheanstalk/pheanstalk_init.php');

/**
 * @Flow\Scope("singleton")
 */
class EventService {

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
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 */
	public function publish($event) {
		$event = new \Ag\Event\Domain\Model\StoredEvent($event);
		$this->storedEventRepository->add($event);
	}

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 */
	public function _publish($event) {
		foreach ($this->settings['listeners'] as $key => $sync) {
			if ($sync === 'async') {
				$this->_asyncPublish($event, $key);
			} else {
				$this->_syncPublish($event, $key);
			}
		}
	}

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 * @param string $key
	 */
	public function _syncPublish($event, $key) {
		$this->systemLogger->log('Syncronously publishing event #' . $event->getEventId() . ' by key ' . $key, LOG_DEBUG);
		$this->dispatcher->dispatch('Ag\Event\Service\EventService', $key, array($event->getEvent()));
	}

	/**
	 * @param \Ag\Event\Domain\Model\StoredEvent $event
	 * @param string $key
	 */
	protected function _asyncPublish($event, $key) {
		$this->systemLogger->log('Asyncronously publishing event #' . $event->getEventId() . ' to tube ' . $key, LOG_DEBUG);
		$pheanstalk = new \Pheanstalk_Pheanstalk('127.0.0.1');
		$pheanstalk->useTube($key)->put(serialize($event));
	}
}

?>