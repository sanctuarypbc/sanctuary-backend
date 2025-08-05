<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvocateServiceTypeRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class AdvocateServiceType extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdvocateDetail", mappedBy="serviceType")
     */
    private $advocateDetails;

    /**
     * AdvocateServiceType constructor.
     */
    public function __construct()
    {
        $this->advocateDetails = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AdvocateServiceType
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|AdvocateDetail[]
     */
    public function getAdvocateDetails(): Collection
    {
        return $this->advocateDetails;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return AdvocateServiceType
     */
    public function addAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if (!$this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails[] = $advocateDetail;
            $advocateDetail->setServiceType($this);
        }

        return $this;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return AdvocateServiceType
     */
    public function removeAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if ($this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails->removeElement($advocateDetail);
            // set the owning side to null (unless already changed)
            if ($advocateDetail->getServiceType() === $this) {
                $advocateDetail->setServiceType(null);
            }
        }

        return $this;
    }
}
