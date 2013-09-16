<?php
namespace Ag\Event\EventHandler;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
abstract class ConventionBasedEventHandler implements EventHandler {

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

	/**
	 * @param \Ag\Event\Domain\Model\DomainEvent $event
	 * @return void
	 */
	public function handle(\Ag\Event\Domain\Model\DomainEvent $event) {

		$namespaceparts = explode('\\', get_class($event));

		$eventName = array_pop($namespaceparts);

		if(array_pop($namespaceparts) !== 'Event' || array_pop($namespaceparts) !== 'Domain' ) {
			$this->systemLogger->log('Event "'.get_class($event).'" did not follow the convention.', LOG_WARNING);
			return;
		}

		$eventHandlerParts = explode('\\', get_called_class());

		array_pop($eventHandlerParts);

		$eventHandlerParts = array_merge($eventHandlerParts, $namespaceparts, array($eventName.'Handler'));

		$eventHandlerClass = '\\'.implode('\\', $eventHandlerParts);

		if(!class_exists($eventHandlerClass)) {
			return;
		}

		$eventHandler = $this->objectManager->get($eventHandlerClass);
		if(!method_exists($eventHandler, 'handle')) {
			$this->systemLogger->log('EventHandler ' . $eventHandlerClass .' did not have a handle() method', LOG_WARNING);
			return;
		}

		$eventHandler->handle($event);
	}

}
?>