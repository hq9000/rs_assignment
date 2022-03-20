<?php

namespace Roadsurfer\Entity;

use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Entity\Mixin\HavingName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EquipmentType
{
    use HavingId;
    use HavingName;
}