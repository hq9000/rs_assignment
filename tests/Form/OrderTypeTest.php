<?php


namespace Roadsurfer\Tests\Form;


use DateTime;
use Roadsurfer\Entity\Order;
use Roadsurfer\Form\OrderType;
use Roadsurfer\Tests\Base\DbTestCase;
use Roadsurfer\Util\DayCodeUtil;
use Symfony\Component\Form\FormFactory;

class OrderTypeTest extends DbTestCase
{


    public function testHappySubmission()
    {

        $toothBrush = $this->createEquipmentType('Tooth brush');
        $towel      = $this->createEquipmentType('Towel');

        $berlin    = $this->createStation('Berlin');
        $frankfurt = $this->createStation('Frankfurt');

        $todayDateTime    = new DateTime('now');
        $tomorrowDateTime = clone $todayDateTime;
        $tomorrowDateTime->modify('+1 day');

        $todayDayCode    = DayCodeUtil::generateDayCode($todayDateTime);
        $tomorrowDayCode = DayCodeUtil::generateDayCode($tomorrowDateTime);

        $counterGridService = $this->getCounterGridServiceFromContainer();
        $counterGridService->extendCounterGrid(3);
        $counterGridService->applyEquipmentShipment($berlin, $toothBrush, $todayDayCode, 100);
        $counterGridService->applyEquipmentShipment($berlin, $towel, $todayDayCode, 100);
        $counterGridService->getEntityManager()->clear();

        /** @var FormFactory $formFactory */
        $formFactory = self::$kernel->getContainer()->get('form.factory');
        $order       = new Order;

        $form = $formFactory->create(OrderType::class, $order);

        $data = [
            'startStation'           => $berlin->getId(),
            'endStation'             => $frankfurt->getId(),
            'startDayCode'           => $todayDayCode,
            'endDayCode'             => $tomorrowDayCode,
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $toothBrush->getId(),
                    'count'         => 10,
                ],
                [
                    'equipmentType' => $towel->getId(),
                    'count'         => 20,
                ],
            ],
        ];

        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
    }


}
