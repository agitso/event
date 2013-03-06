<?php
namespace Ag\Event;

use \TYPO3\Flow\Package\Package as BasePackage;

class Package extends BasePackage {

	/**
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();

		$dispatcher->connect(
			'Ag\Event\Domain\Model\StoredEvent', 'eventPersisted',
			'Ag\Event\Service\EventService', '_publish'
		);
	}
}
?>