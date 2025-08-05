<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvocateDetailRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class AdvocateDetail extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $additionalPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emergencyContact;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="advocateDetails")
     */
    private $organization;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="advocateDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClientDetail", mappedBy="advocate")
     */
    private $clientDetails;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="advocateDetails")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdvocateServiceType", inversedBy="advocateDetails")
     */
    private $serviceType;

    /**
     * AdvocateDetail constructor.
     */
    public function __construct()
    {
        $this->language = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $Identifier
     * @return AdvocateDetail
     */
    public function setIdentifier(?string $Identifier): self
    {
        $this->identifier = $Identifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdditionalPhone(): ?string
    {
        return $this->additionalPhone;
    }

    /**
     * @param string|null $additionalPhone
     * @return AdvocateDetail
     */
    public function setAdditionalPhone(?string $additionalPhone): self
    {
        $this->additionalPhone = $additionalPhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmergencyContact(): ?string
    {
        return $this->emergencyContact;
    }

    /**
     * @param string|null $emergencyContact
     * @return AdvocateDetail
     */
    public function setEmergencyContact(?string $emergencyContact): self
    {
        $this->emergencyContact = $emergencyContact;

        return $this;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    /**
     * @param Organization|null $organization
     * @return AdvocateDetail
     */
    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

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
     * @param User $user
     * @return AdvocateDetail
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguage(): Collection
    {
        return $this->language;
    }

    /**
     * @param Language $language
     * @return AdvocateDetail
     */
    public function addLanguage(Language $language): self
    {
        if (!$this->language->contains($language)) {
            $this->language[] = $language;
        }

        return $this;
    }

    /**
     * @param Language $language
     * @return AdvocateDetail
     */
    public function removeLanguage(Language $language): self
    {
        if ($this->language->contains($language)) {
            $this->language->removeElement($language);
        }

        return $this;
    }

    public function getServiceType(): ?AdvocateServiceType
    {
        return $this->serviceType;
    }

    public function setServiceType(?AdvocateServiceType $serviceType): self
    {
        $this->serviceType = $serviceType;

        return $this;
    }
}
