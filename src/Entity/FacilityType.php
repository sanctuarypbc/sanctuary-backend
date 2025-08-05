<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacilityTypeRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class FacilityType extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Facility", mappedBy="FacilityType")
     */
    private $facilities;

    /**
     * FacilityType constructor.
     */
    public function __construct()
    {
        $this->facilities = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return FacilityType
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Facility[]
     */
    public function getFacilities(): Collection
    {
        return $this->facilities;
    }

    /**
     * @param Facility $facility
     * @return FacilityType
     */
    public function addFacility(Facility $facility): self
    {
        if (!$this->facilities->contains($facility)) {
            $this->facilities[] = $facility;
            $facility->setFacilityType($this);
        }

        return $this;
    }

    /**
     * @param Facility $facility
     * @return FacilityType
     */
    public function removeFacility(Facility $facility): self
    {
        if ($this->facilities->contains($facility)) {
            $this->facilities->removeElement($facility);
            // set the owning side to null (unless already changed)
            if ($facility->getFacilityType() === $this) {
                $facility->setFacilityType(null);
            }
        }

        return $this;
    }
}
