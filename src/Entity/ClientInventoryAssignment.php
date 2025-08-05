<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientInventoryAssignmentRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientInventoryAssignment extends AbstractEntity
{
    /**
     * @ORM\Column(type="datetime")
     */
    private $assignedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checkInAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checkOutAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientDetail")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientDetail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FacilityInventory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $facilityInventory;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @return \DateTimeInterface|null
     */
    public function getAssignedAt(): ?\DateTimeInterface
    {
        return $this->assignedAt;
    }

    /**
     * @param \DateTimeInterface $assignedAt
     * @return $this
     */
    public function setAssignedAt(\DateTimeInterface $assignedAt): self
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCheckInAt(): ?\DateTimeInterface
    {
        return $this->checkInAt;
    }

    /**
     * @param \DateTimeInterface|null $checkInAt
     * @return $this
     */
    public function setCheckInAt(?\DateTimeInterface $checkInAt): self
    {
        $this->checkInAt = $checkInAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCheckOutAt(): ?\DateTimeInterface
    {
        return $this->checkOutAt;
    }

    /**
     * @param \DateTimeInterface|null $checkOutAt
     * @return $this
     */
    public function setCheckOutAt(?\DateTimeInterface $checkOutAt): self
    {
        $this->checkOutAt = $checkOutAt;

        return $this;
    }

    /**
     * @return ClientDetail|null
     */
    public function getClientDetail(): ?ClientDetail
    {
        return $this->clientDetail;
    }

    /**
     * @param ClientDetail|null $clientDetail
     * @return $this
     */
    public function setClientDetail(?ClientDetail $clientDetail): self
    {
        $this->clientDetail = $clientDetail;

        return $this;
    }

    /**
     * @return FacilityInventory|null
     */
    public function getFacilityInventory(): ?FacilityInventory
    {
        return $this->facilityInventory;
    }

    /**
     * @param FacilityInventory|null $facilityInventory
     * @return $this
     */
    public function setFacilityInventory(?FacilityInventory $facilityInventory): self
    {
        $this->facilityInventory = $facilityInventory;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
