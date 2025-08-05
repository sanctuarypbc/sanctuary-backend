<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacilityInventoryRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class FacilityInventory extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     */
    private $totalAvailable;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $availabilityUpdateAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FacilityInventoryType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $inventoryType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Facility", inversedBy="facilityInventories")
     */
    private $facility;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return FacilityInventory
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param int|null $capacity
     * @return FacilityInventory
     */
    public function setCapacity($capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalAvailable()
    {
        return $this->totalAvailable;
    }

    /**
     * @param int|null $totalAvailable
     * @return FacilityInventory
     */
    public function setTotalAvailable($totalAvailable): self
    {
        $this->totalAvailable = $totalAvailable;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAvailabilityUpdateAt()
    {
        return $this->availabilityUpdateAt;
    }

    /**
     * @param \DateTime|null $availabilityUpdateAt
     * @return FacilityInventory
     */
    public function setAvailabilityUpdateAt($availabilityUpdateAt): self
    {
        $this->availabilityUpdateAt = $availabilityUpdateAt;

        return $this;
    }

    /**
     * @return FacilityInventoryType
     */
    public function getInventoryType()
    {
        return $this->inventoryType;
    }

    /**
     * @param FacilityInventoryType $inventoryType
     * @return FacilityInventory
     */
    public function setInventoryType($inventoryType): self
    {
        $this->inventoryType = $inventoryType;

        return $this;
    }

    /**
     * @return Facility|null
     */
    public function getFacility(): ?Facility
    {
        return $this->facility;
    }

    /**
     * @param Facility|null $facility
     * @return $this
     */
    public function setFacility(?Facility $facility): self
    {
        $this->facility = $facility;

        return $this;
    }
}