<?php
namespace Ag\Event\Tests\Functional\EventService;

use TYPO3\Flow\Tests\FunctionalTestCase;

/**
 */
class StoreAndPublishTest extends FunctionalTestCase {

	/**
	 * @var boolean
	 */
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
	 * @var \Ag\Event\Store\PersistenceStore\StoredEventRepository
	 */
	protected $storedEventRepository;

	/**
	 * @var \Ag\Event\EventHandler\StubEventHandler
	 */
	protected $stubEventHandler;

	/**
	 */
	public function setUp() {
		parent::setUp();

		$this->eventService = $this->objectManager->get('Ag\Event\Service\EventService');
		$this->stubEventHandler = $this->objectManager->get('Ag\Event\EventHandler\StubEventHandler');
		$this->storedEventRepository = $this->objectManager->get('Ag\Event\Domain\Repository\StoredEventRepository');
	}

	/**
	 * @test
	 */
	public function test() {
		$event = new TestEvent('Hello world #1');
		$this->eventService->publish($event);

		$event = new TestEvent('Hello world #2');
		$this->eventService->publish($event);

		$this->persistenceManager->persistAll();
		$this->persistenceManager->clearState();

		$this->assertCount(2, $this->stubEventHandler->events);
		$this->assertInstanceOf('Ag\Event\Tests\Functional\EventService\TestEvent', $this->stubEventHandler->events[0]);
		$this->assertInstanceOf('Ag\Event\Tests\Functional\EventService\TestEvent', $this->stubEventHandler->events[1]);
		$this->assertEquals('Hello world #1', $this->stubEventHandler->events[0]->getMessage());
		$this->assertEquals('Hello world #2', $this->stubEventHandler->events[1]->getMessage());

		// Assert persistence
		$this->assertEquals(2, $this->storedEventRepository->countAll());

		$event = $this->storedEventRepository->findByIdentifier('1');
		$this->assertEquals('Hello world #1', $event->getEvent()->getMessage());

		$event = $this->storedEventRepository->findByIdentifier('2');
		$this->assertEquals('Hello world #2', $event->getEvent()->getMessage());
	}

}
