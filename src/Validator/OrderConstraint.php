<?php

namespace Roadsurfer\Validator;

use Symfony\Component\Validator\Constraint;

class OrderConstraint extends Constraint
{
    const ORDER_END_DAY_IS_BEFORE_START_DAY = "750eae8f-0c51-4324-a381-6d40851432ed";
    const ORDER_START_DAY_IS_IN_PAST = "207a44d0-b93f-4f57-9fac-11ff4d63acd8";
    const ORDER_END_DAY_IS_TOO_FAR_IN_FUTURE = "a87cc608-df42-4fc7-80b5-4bc1f8a3b64c";
    const WILL_RUN_OUT_OF_EQUIPMENT_CODE = "a9d2fe79-9b6b-4da7-b082-9c6fe3079f14";
    const MORE_THAN_ONE_ENTRY_FOR_EQUIPMENT_TYPE = "059e2e42-d276-4c60-a991-bd13db3a352c";

    public static string $willRunOutOfEquipmentMessage = "placing this order will make station {{ station_name }} run out of equipment {{ equipment_type_name }} on day {{ day_code }}!";
    public static string $orderEndDayIsBeforeStartDayMessage = "order end day is before start day";
    public static string $orderStartIsInPastMessage = "order start is in the past";
    public static string $orderEndDayIsTooFarInFutureMessage = "order end day is too far in the future";
    public static string $moreThanOneEntryForEquipmentType = "order has more than one entry for equipment type {{ equipment_type_name }}";
}