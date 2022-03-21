<?php


namespace Roadsurfer\Tests\Form;


use Roadsurfer\Form\OrderType;
use Roadsurfer\Tests\Base\DbTestCase;
use Symfony\Component\Form\FormFactory;

class OrderTypeTest extends DbTestCase
{
    public function testSomething()
    {
        $toothBrush = $this->createEquipmentType('Tooth brush');
        $towel      = $this->createEquipmentType('Towel');

        $berlin    = $this->createStation('Berlin');
        $frankfurt = $this->createStation('Frankfurt');


        /** @var FormFactory $formFactory */
        $formFactory = self::$kernel->getContainer()->get('form.factory');
        $form = $formFactory->create(OrderType::class);

        $data = [
            'startStation' => $berlin->getId(),
            'endStation' => $frankfurt->getId(),
            'startDayCode' => '20220320',
            'endDayCode' => '20220323',
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $toothBrush->getId(),
                    'count' => 10
                ],
                [
                    'equipmentType' => $towel->getId(),
                    'count' => 20
                ]
            ]
        ];

        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());


    }

}