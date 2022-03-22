<?php


namespace Roadsurfer\Tests\Service;

use DateTime;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\OrderEquipmentCounter;
use Roadsurfer\Entity\Station;
use Roadsurfer\Service\CounterGridService;
use Roadsurfer\Service\CurrentTimeProviderInterface;
use Roadsurfer\Tests\Base\DbTestCase;
use Roadsurfer\Util\ReportDataProducer;

/**
 * Class GridServiceTest
 *
 * @package Roadsurfer\Tests\Service
 */
class GridServiceTest extends DbTestCase
{
    public function testExtendGrid()
    {
        $this->createStation('Berlin');
        $this->createEquipmentType('Tooth Brush');

        $this->assertEquals(0, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));

        $service = new CounterGridService();
        $service->setEntityManager(self::$entityManager);
        $service->setCurrentTimeProvider($this->createMockTimeProvider(new DateTime('2022-03-20 19:21 MSK')));

        $service->extendCounterGrid(10);
        $this->assertEquals(22, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));

        $service->extendCounterGrid(20);
        $this->assertEquals(42, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));

        $service->extendCounterGrid(20);
        $this->assertEquals(42, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));

        $service->setCurrentTimeProvider($this->createMockTimeProvider(new DateTime('2022-03-23 19:21 MSK')));
        $service->extendCounterGrid(20);
        $this->assertEquals(48, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));
    }


    public function testApplyShipmentAndOrder()
    {
        $berlin     = $this->createStation('Berlin');
        $frankfurt  = $this->createStation('Frankfurt');
        $toothBrush = $this->createEquipmentType('Tooth Brush');

        $this->assertEquals(0, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));

        $service = new CounterGridService();
        $service->setEntityManager(self::$entityManager);
        $service->setCurrentTimeProvider($this->createMockTimeProvider(new DateTime('2022-03-20 19:21 MSK')));

        $service->extendCounterGrid(5);
        $reportProducer = new ReportDataProducer();

        $allCounters = self::$entityManager->getRepository(AbstractDailyStationEquipmentCounter::class)->findAll();
        $reportData  = $reportProducer->produceReportData($allCounters);

        $expectedReportData = [
            'Berlin'    =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
            'Frankfurt' =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedReportData, $reportData);

        $service->applyEquipmentShipment($berlin, $toothBrush, "20220321", 10);

        self::$entityManager->clear();
        $allCounters = self::$entityManager->getRepository(AbstractDailyStationEquipmentCounter::class)->findAll();
        $reportData  = $reportProducer->produceReportData($allCounters);

        $expectedReportData = [
            'Berlin'    =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                        ],
                ],
            'Frankfurt' =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedReportData, $reportData);

        $service->extendCounterGrid(6);

        self::$entityManager->clear();
        $allCounters = self::$entityManager->getRepository(AbstractDailyStationEquipmentCounter::class)->findAll();
        $reportData  = $reportProducer->produceReportData($allCounters);

        $expectedReportData = [
            'Berlin'    =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220326 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                        ],
                ],
            'Frankfurt' =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220326 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedReportData, $reportData);

        $order = new Order();
        $order->setStartDayCode('20220324');
        $order->setEndDayCode('20220326');
        $order->setStartStation($berlin);
        $order->setEndStation($frankfurt);

        $orderEquipmentCounter1 = new OrderEquipmentCounter();
        $orderEquipmentCounter1->setEquipmentType($toothBrush);
        $orderEquipmentCounter1->setCount(5);

        $order->setOrderEquipmentCounters([$orderEquipmentCounter1]);

        $service->applyOrder($order);

        self::$entityManager->clear();
        $allCounters = self::$entityManager->getRepository(AbstractDailyStationEquipmentCounter::class)->findAll();
        $reportData  = $reportProducer->produceReportData($allCounters);

        $expectedReportData = [
            'Berlin'    =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 10,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 5,
                                    'booked'  => 5,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 5,
                                    'booked'  => 0,
                                ],
                            20220326 =>
                                [
                                    'on hand' => 5,
                                    'booked'  => 0,
                                ],
                        ],
                ],
            'Frankfurt' =>
                [
                    'Tooth Brush' =>
                        [
                            20220320 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220321 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220325 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220326 =>
                                [
                                    'on hand' => 5,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedReportData, $reportData);
    }


    private function createMockTimeProvider(DateTime $fixedDateTime): CurrentTimeProviderInterface
    {
        $mock = $this->createMock(CurrentTimeProviderInterface::class);
        $mock->method('getCurrentDateTime')->willReturnCallback(
            function () use ($fixedDateTime) {
                return clone $fixedDateTime;
            }
        );

        return $mock;
    }

}