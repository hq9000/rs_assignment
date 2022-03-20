<?php


namespace Roadsurfer\Validator;


use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\DependencyInjection\CurrentTimeProviderAware;
use Roadsurfer\Entity\Order;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrderConstraintValidator extends ConstraintValidator
{

    use CounterGridServiceAware;
    use CurrentTimeProviderAware;

    public function validate($value, Constraint $constraint)
    {
        assert($value instanceof Order);
        assert($constraint instanceof OrderConstraint);

        $order = $value; # syntactic sugar... sweet!

        $this->validateDates($order, $constraint);
        $this->validateEquipmentAvailability($order, $constraint);
    }

    private function validateDates(Order $order, OrderConstraint $constraint)
    {
    }

    private function validateEquipmentAvailability(Order $order, OrderConstraint $constraint)
    {
        foreach ($order->getOrderEquipmentCounters() as $orderEquipmentCounter) {
            $onHandCounters = $this->getCounterGridService()->getOnHandCounters(
                $order->getStartStation(),
                $orderEquipmentCounter->getEquipmentType(),
                $order->getStartDayCode(),
                $order->getEndDayCode()
            );

            foreach ($onHandCounters as $counter) {
                if ($counter->getCount() < $orderEquipmentCounter->getCount()) {
                    $this->context->buildViolation(
                        OrderConstraint::$willRunOutOfEquipmentMessage,
                        [
                            '{{ station_name }}'        => $order->getStartStation()->getName(),
                            '{{ equipment_type_name }}' => $counter->getEquipmentType()->getName(),
                            '{{ day_code }}'            => $counter->getDayCode(),
                        ]
                    )->setCode(OrderConstraint::WILL_RUN_OUT_OF_EQUIPMENT_CODE)->addViolation();
                }
            }
        }
    }
}