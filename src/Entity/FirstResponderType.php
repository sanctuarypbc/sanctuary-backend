<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FirstResponderTypeRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class FirstResponderType extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FirstResponderDetail", mappedBy="firstResponderType")
     */
    private $firstResponderDetails;

    /**
     * FirstResponderType constructor.
     */
    public function __construct()
    {
        $this->firstResponderDetails = new ArrayCollection();
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
     * @return FirstResponderType
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|FirstResponderDetail[]
     */
    public function getFirstResponderDetails(): Collection
    {
        return $this->firstResponderDetails;
    }

    /**
     * @param FirstResponderDetail $firstResponderDetail
     * @return FirstResponderType
     */
    public function addFirstResponderDetail(FirstResponderDetail $firstResponderDetail): self
    {
        if (!$this->firstResponderDetails->contains($firstResponderDetail)) {
            $this->firstResponderDetails[] = $firstResponderDetail;
            $firstResponderDetail->setFirstResponderType($this);
        }

        return $this;
    }

    /**
     * @param FirstResponderDetail $firstResponderDetail
     * @return FirstResponderType
     */
    public function removeFirstResponderDetail(FirstResponderDetail $firstResponderDetail): self
    {
        if ($this->firstResponderDetails->contains($firstResponderDetail)) {
            $this->firstResponderDetails->removeElement($firstResponderDetail);
            // set the owning side to null (unless already changed)
            if ($firstResponderDetail->getFirstResponderType() === $this) {
                $firstResponderDetail->setFirstResponderType(null);
            }
        }

        return $this;
    }
}
