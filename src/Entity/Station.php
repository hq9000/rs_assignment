<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Traits\HavingId;
use Roadsurfer\Entity\Traits\HavingName;

#[ORM\Entity()]
class Station
{
    use HavingId;
    use HavingName;
}