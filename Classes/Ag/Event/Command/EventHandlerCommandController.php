<?php
namespace Ag\Event\Command;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 */
class EventHandlerCommandController extends CommandController {

	/**
	 * @Flow\Inject(setting="eventHandlers")
	 * @var array
	 */
	protected $eventHandlersConfiguration = array();

	/**
	 * Lists all configured event handlers
	 */
	public function listCommand() {
		$tableData = array();

		foreach ($this->eventHandlersConfiguration as $syncType => $configuration) {
			foreach ($configuration as $implementationClassName => $enabledStatus) {
				$tableData[] = array(
					$implementationClassName . PHP_EOL . '  Key: <b>' . str_replace('\\', '_', $implementationClassName) . '</b>',
					$syncType,
					$enabledStatus ? 'TRUE' : 'FALSE'
				);
			}
		}

		$this->output->outputTable($tableData, array('Implementation class / key', 'Type', 'Enabled?'));
	}

}
