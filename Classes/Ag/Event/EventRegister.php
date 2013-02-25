<?php
namespace Ag\Event;

class EventRegister {

	/**
	 * @param \TYPO3\Flow\SignalSlot\Dispatcher $dispatcher
	 * @param string $eventClass
	 * @param string $targetClass
	 * @param string $targetMethod
	 */
	public static function listenFor($dispatcher, $eventClass, $targetClass, $targetMethod) {
		$dispatcher->connect('Ag\Event\Service\EventService', $eventClass, $targetClass, $targetMethod);
	}

}
?>