<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Entity\Mixin\HavingName;

#[ORM\Entity()]
class Station
{
    use HavingId;
    use HavingName;
}