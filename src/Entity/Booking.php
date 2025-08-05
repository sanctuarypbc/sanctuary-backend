<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Booking extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $performed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $checkIn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $checkOut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Facility")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $facility;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $notes;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $roomNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FacilityInventoryType")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $facilityInventoryType;

    /**
     * @return mixed
     */
    public function getPerformed()
    {
        return $this->performed;
    }

    /**
     * @param mixed $performed
     */
    public function setPerformed($performed): void
    {
        $this->performed = $performed;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCheckIn(): ?\DateTimeInterface
    {
        return $this->checkIn;
    }

    /**
     * @param \DateTimeInterface $checkIn
     * @return $this
     */
    public function setCheckIn(\DateTimeInterface $checkIn): self
    {
        $this->checkIn = $checkIn;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCheckOut(): ?\DateTimeInterface
    {
        return $this->checkOut;
    }

    /**
     * @param \DateTimeInterface $checkOut
     * @return $this
     */
    public function setCheckOut(\DateTimeInterface $checkOut): self
    {
        $this->checkOut = $checkOut;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    /**
     * @return FacilityInventoryType|null
     */
    public function getFacilityInventoryType(): ?FacilityInventoryType
    {
        return $this->facilityInventoryType;
    }

    /**
     * @param FacilityInventoryType|null $facilityInventoryType
     * @return $this
     */
    public function setFacilityInventoryType(?FacilityInventoryType $facilityInventoryType): self
    {
        $this->facilityInventoryType = $facilityInventoryType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return $this
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoomNumber(): ?string
    {
        return $this->roomNumber;
    }

    /**
     * @param string|null $roomNumber
     * @return $this
     */
    public function setRoomNumber(?string $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

}
