<?php


namespace Roadsurfer\Tests\Validator;


use DateTime;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\OrderEquipmentCounter;
use Roadsurfer\Service\CounterGridService;
use Roadsurfer\Service\CurrentTimeProvider;
use Roadsurfer\Tests\Base\DbTestCase;
use Roadsurfer\Util\DayCodeUtil;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class OrderValidationTest extends DbTestCase
{
    public function testValidateEmptyOrder()
    {
        $currentTimeProvider = self::getContainer()->get(CurrentTimeProvider::class);

        $now = DateTime::createFromFormat('Y-m-d H:i:s', '2022-03-21 06:32:33');
        $currentTimeProvider->setFixatedTime($now);
        $validator = self::getContainer()->get('validator');

        $order = new Order;

        $violations = $validator->validate($order);
        $digest     = $this->produceViolationDigest($violations);

        $expectedDigest = [
            'startDayCode' => ['This value should not be null.'],
            'endDayCode'   => ['This value should not be null.'],
            'startStation' => ['This value should not be null.'],
            'endStation'   => ['This value should not be null.'],
        ];

        $this->assertEquals($expectedDigest, $digest);
    }

    public function testSystemNotReady_ThenRunningOutOfEquipment_ThenHappy()
    {
        $berlin     = $this->createStation('Berlin');
        $toothBrush = $this->createEquipmentType('Tooth brush');

        $now = DateTime::createFromFormat('Y-m-d H:i:s', '2022-03-21 06:32:33');
        self::getContainer()->get(CurrentTimeProvider::class)->setFixatedTime($now);

        $today    = $now;
        $tomorrow = clone($today);
        $tomorrow->modify("+1 day");

        $todayDayCode    = DayCodeUtil::generateDayCode($now);
        $tomorrowDayCode = DayCodeUtil::generateDayCode($tomorrow);

        $order = new Order();
        $order->setStartStation($berlin);
        $order->setEndStation($berlin);

        $equipmentCounter = new OrderEquipmentCounter();
        $equipmentCounter->setEquipmentType($toothBrush);
        $equipmentCounter->setCount(5);

        $order->setOrderEquipmentCounters([$equipmentCounter]);

        $order->setStartDayCode($todayDayCode);
        $order->setEndDayCode($tomorrowDayCode);

        $validator  = self::getContainer()->get('validator');
        $violations = $validator->validate($order);
        $digest     = $this->produceViolationDigest($violations);

        $expectedDigest = [
            '' =>
                [
                    0 => 'System is not ready yet to process your order, please try again later',
                ],
        ];

        $this->assertEquals($expectedDigest, $digest);

        $counterGridService = $this->getContainer()->get(CounterGridService::class);
        $counterGridService->extendCounterGrid(100);

        $violations = $validator->validate($order);
        $digest     = $this->produceViolationDigest($violations);

        $expectedDigest = [
            '' =>
                [
                    0 => 'Placing this order would make station "Berlin" run out of equipment "Tooth brush" on day 20220321!',
                ],
        ];

        $this->assertEquals($expectedDigest, $digest);
        $counterGridService->applyEquipmentShipment($berlin, $toothBrush, $todayDayCode, 5);
        $counterGridService->getEntityManager()->clear();

        $violations = $validator->validate($order);
        $digest     = $this->produceViolationDigest($violations);
        $this->assertEquals([], $digest);

    }

    private function produceViolationDigest(ConstraintViolationList $violations): array
    {
        $res = [];
        /** @var ConstraintViolation[] $violations */
        foreach ($violations as $violation) {
            $res[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $res;
    }

}