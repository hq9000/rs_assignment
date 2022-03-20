<?php

namespace Roadsurfer\Entity;

use Roadsurfer\Entity\Traits\HavingId;
use Roadsurfer\Entity\Traits\HavingName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EquipmentType
{
    use HavingId;
    use HavingName;
}