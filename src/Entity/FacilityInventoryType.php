<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacilityInventoryTypeRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class FacilityInventoryType extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Facility")
     * @ORM\JoinColumn(nullable=false)
     */
    private $facility;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return FacilityInventoryType
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFacility()
    {
        return $this->facility;
    }

    /**
     * @param $facility
     * @return FacilityInventoryType
     */
    public function setFacility($facility): self
    {
        $this->facility = $facility;
        return $this;
    }
}