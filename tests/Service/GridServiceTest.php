<?php


namespace Roadsurfer\Tests\Service;

use DateTime;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Station;
use Roadsurfer\Service\CounterGridService;
use Roadsurfer\Service\CurrentTimeProviderInterface;
use Roadsurfer\Tests\Base\DbTestCase;


class GridServiceTest extends DbTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testExtend()
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

        $this->removeAll(EquipmentType::class);
        $this->removeAll(Station::class);
        $this->assertEquals(0, $this->getEntityCount(AbstractDailyStationEquipmentCounter::class));
    }

    private function createMockTimeProvider(DateTime $fixedDateTime): CurrentTimeProviderInterface
    {
        $mock = $this->createMock(CurrentTimeProviderInterface::class);
        $mock->method('getCurrentDateTime')->willReturnCallback(function() use($fixedDateTime) {
            return clone $fixedDateTime;
        });
        return $mock;
    }

}