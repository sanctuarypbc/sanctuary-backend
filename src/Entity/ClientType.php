<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientTypeRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientType extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClientDetail", mappedBy="clientType")
     */
    private $clientDetails;

    /**
     * ClientType constructor.
     */
    public function __construct()
    {
        $this->clientDetails = new ArrayCollection();
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
     * @return ClientType
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|ClientDetail[]
     */
    public function getClientDetails(): Collection
    {
        return $this->clientDetails;
    }

    /**
     * @param ClientDetail $clientDetail
     * @return ClientType
     */
    public function addClientDetail(ClientDetail $clientDetail): self
    {
        if (!$this->clientDetails->contains($clientDetail)) {
            $this->clientDetails[] = $clientDetail;
            $clientDetail->setClientType($this);
        }

        return $this;
    }

    /**
     * @param ClientDetail $clientDetail
     * @return ClientType
     */
    public function removeClientDetail(ClientDetail $clientDetail): self
    {
        if ($this->clientDetails->contains($clientDetail)) {
            $this->clientDetails->removeElement($clientDetail);
            // set the owning side to null (unless already changed)
            if ($clientDetail->getClientType() === $this) {
                $clientDetail->setClientType(null);
            }
        }

        return $this;
    }
}
