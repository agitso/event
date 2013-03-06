<?php
namespace Ag\Event\Tests\Functional\EventService;

class StoreAndPublishTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @var \Ag\Event\Service\EventService
	 */
	protected $eventService;

	/**
	 * @var \TYPO3\Flow\SignalSlot\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var \Ag\Event\Domain\Repository\StoredEventRepository
	 */
	protected $storedEventRepository;

	/**
	 * @var \Ag\Event\Tests\Functional\EventService\EventListener
	 */
	protected $eventListener;

	public function setUp() {
		parent::setUp();


		$this->dispatcher = $this->objectManager->get('TYPO3\Flow\SignalSlot\Dispatcher');
		$this->dispatcher->connect(
			'Ag\Event\Service\EventService', 'test',
			'Ag\Event\Tests\Functional\EventService\EventListener', 'listen'
		);

		$this->eventService = $this->objectManager->get('Ag\Event\Service\EventService');
		$this->eventService->injectSettings(array('listeners'=>array('test'=>'sync')));

		$this->eventListener = $this->objectManager->get('Ag\Event\Tests\Functional\EventService\EventListener');

		$this->storedEventRepository = $this->objectManager->get('Ag\Event\Domain\Repository\StoredEventRepository');
	}

	/**
	 * @test
	 */
	public function canPersistEvent() {
		$event = new TestEvent('Hello world #1');
		$this->eventService->publish($event);

		$event = new TestEvent('Hello world #2');
		$this->eventService->publish($event);

		$this->persistenceManager->persistAll();
		$this->persistenceManager->clearState();

		// Assert persistence
		$this->assertEquals(2, $this->storedEventRepository->countAll());

		$event = $this->storedEventRepository->findByIdentifier('1');
		$this->assertEquals('Hello world #1', $event->getEvent()->getMessage());

		$event = $this->storedEventRepository->findByIdentifier('2');
		$this->assertEquals('Hello world #2', $event->getEvent()->getMessage());

		$this->assertEquals(2, count($this->eventListener->events));

		$event = $this->eventListener->events[0];
		$this->assertEquals('Hello world #1', $event->getMessage());

		$event = $this->eventListener->events[1];
		$this->assertEquals('Hello world #2', $event->getMessage());
	}

}

?>