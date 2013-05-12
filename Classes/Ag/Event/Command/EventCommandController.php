<?php
namespace Ag\Event\Command;

use TYPO3\Flow\Annotations as Flow;

require_once(FLOW_PATH_PACKAGES . '/Libraries/pda/pheanstalk/pheanstalk_init.php');


/**
 * @Flow\Scope("singleton")
 */
class EventCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \Ag\Event\Service\EventService
	 * @Flow\Inject
	 */
	protected $eventService;

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $systemLogger;

	/**
	 * @param string $key
	 */
	public function processCommand($key) {
		$pheanstalk = new \Pheanstalk_Pheanstalk('127.0.0.1');

		while(TRUE) {
			$this->systemLogger->log('Waiting for event in tube ' . $key, LOG_DEBUG);
			$job = $pheanstalk
				  ->watch($key)
				  ->ignore('default')
				  ->reserve();

			$this->eventService->_syncPublish(unserialize($job->getData()), str_replace('_', '\\', $key));

			$pheanstalk->delete($job);
		}
	}
}

?>