<?php
namespace Ag\Event\EventHandler;

use Ag\Event\Domain\Model\DomainEvent;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 * @Flow\Scope("singleton")
 */
abstract class ConventionBasedEventHandler implements EventHandler {

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @param DomainEvent $event
	 * @return void
	 */
	public function handle(DomainEvent $event) {

		$namespaceParts = explode('\\', get_class($event));
		$eventName = array_pop($namespaceParts);

		if(array_pop($namespaceParts) !== 'Event' || array_pop($namespaceParts) !== 'Domain' ) {
			$this->systemLogger->log(sprintf('Event "%s" did not follow the convention.', $this->reflectionService->getClassNameByObject($event)), LOG_WARNING);
			return;
		}

		$eventHandlerParts = explode('\\', get_called_class());
		array_pop($eventHandlerParts);

		$eventHandlerParts = array_merge($eventHandlerParts, $namespaceParts, array($eventName.'Handler'));
		$eventHandlerClassName = '\\'.implode('\\', $eventHandlerParts);

		if(!class_exists($eventHandlerClassName)) {
			return;
		}

		$eventHandler = $this->objectManager->get($eventHandlerClassName);
		if(!method_exists($eventHandler, 'handle')) {
			$this->systemLogger->log(sprintf('EventHandler "%s" did not have a handle() method.', $eventHandlerClassName), LOG_WARNING);
			return;
		}

		$eventHandler->handle($event);
	}

}
