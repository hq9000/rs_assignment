<?php


namespace Roadsurfer\Validator;


use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\DependencyInjection\CurrentTimeProviderAware;
use Roadsurfer\Entity\Order;
use Roadsurfer\Util\DatePolicy;
use Roadsurfer\Util\DayCodeUtil;
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

        if ($this->context->getViolations()) {
            // not checking any further if dates are off
            return;
        }

        $this->validateEquipmentAvailability($order, $constraint);
    }

    private function validateDates(Order $order, OrderConstraint $constraint)
    {
        if ($order->getStartDayCode() <= DayCodeUtil::generateDayCode(
                $this->getCurrentTimeProvider()->getCurrentDateTime()
            )) {
            $this->context->buildViolation(OrderConstraint::$orderStartIsInPastMessage)->setCode(
                OrderConstraint::ORDER_START_DAY_IS_IN_PAST
            )->addViolation();
        }

        $lastPossibleDayCode = $this->getLastPossibleDayCode();

        if ($order->getEndDayCode() >= $lastPossibleDayCode) {
            $this->context->buildViolation(OrderConstraint::$orderEndDayIsTooFarInFutureMessage)->setCode(
                OrderConstraint::ORDER_END_DAY_IS_TOO_FAR_IN_FUTURE
            )->addViolation();
        }

        if ($order->getEndDayCode() < $order->getStartDayCode()) {
            $this->context->buildViolation(OrderConstraint::$orderEndDayIsBeforeStartDayMessage)->setCode(
                OrderConstraint::ORDER_END_DAY_IS_BEFORE_START_DAY
            )->addViolation();
        }
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
                            '{{ station_name }}' => $order->getStartStation()->getName(),
                            '{{ equipment_type_name }}' => $counter->getEquipmentType()->getName(),
                            '{{ day_code }}' => $counter->getDayCode(),
                        ]
                    )->setCode(OrderConstraint::WILL_RUN_OUT_OF_EQUIPMENT_CODE)->addViolation();
                    break;
                }
            }
        }
    }

    private function getLastPossibleDayCode(): string
    {
        $now = $this->getCurrentTimeProvider()->getCurrentDateTime();
        $now->modify("add " . DatePolicy::NUM_FUTURE_DAYS_TO_ENSURE_COUNTER_GRID_FOR . " days");

        return DayCodeUtil::generateDayCode($now);
    }
}