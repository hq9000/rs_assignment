<?php

namespace Roadsurfer\Validator;

use Symfony\Component\Validator\Constraint;

class OrderConstraint extends Constraint
{
    const WILL_RUN_OUT_OF_EQUIPMENT_CODE = "a9d2fe79-9b6b-4da7-b082-9c6fe3079f14";

    public static string $willRunOutOfEquipmentMessage = "placing this order will make station {{ station_name }} run out of equipment {{ equipment_type_name }} on day {{ day_code }}!";

}