<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Organization extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $streetAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactEmail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrganizationType", inversedBy="organizations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizationType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FirstResponderDetail", mappedBy="organization")
     */
    private $firstResponderDetails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdvocateDetail", mappedBy="organization")
     */
    private $advocateDetails;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClientType")
     */
    private $clientTypes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->firstResponderDetails = new ArrayCollection();
        $this->advocateDetails = new ArrayCollection();
        $this->clientTypes = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => ($this->user instanceof  User) ? $this->user->getId() : null,
            'username' => ($this->user instanceof  User) ? $this->user->getUsername() : null,
            'name' => $this->name,
            'street_address' => $this->streetAddress,
            'city' => $this->city,
            'zip_code' => $this->zipCode,
            'state' => $this->state,
            'contact_name' => $this->contactName,
            'contact_phone' => $this->contactPhone,
            'contact_email' => $this->contactEmail,
            'status' => $this->status,
        ];
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
     * @return Organization
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    /**
     * @param string|null $streetAdress
     * @return $this
     */
    public function setStreetAddress(?string $streetAdress): self
    {
        $this->streetAddress = $streetAdress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    /**
     * @param string|null $contactName
     * @return Organization
     */
    public function setContactName(?string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    /**
     * @param string|null $contactPhone
     * @return Organization
     */
    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    /**
     * @param string|null $contactEmail
     * @return Organization
     */
    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return OrganizationType|null
     */
    public function getOrganizationType(): ?OrganizationType
    {
        return $this->organizationType;
    }

    /**
     * @param OrganizationType|null $OrganizationType
     * @return Organization
     */
    public function setOrganizationType(?OrganizationType $OrganizationType): self
    {
        $this->organizationType = $OrganizationType;

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
     * @return Organization
     */
    public function addFirstResponderDetail(FirstResponderDetail $firstResponderDetail): self
    {
        if (!$this->firstResponderDetails->contains($firstResponderDetail)) {
            $this->firstResponderDetails[] = $firstResponderDetail;
            $firstResponderDetail->setOrganization($this);
        }

        return $this;
    }

    /**
     * @param FirstResponderDetail $firstResponderDetail
     * @return Organization
     */
    public function removeFirstResponderDetail(FirstResponderDetail $firstResponderDetail): self
    {
        if ($this->firstResponderDetails->contains($firstResponderDetail)) {
            $this->firstResponderDetails->removeElement($firstResponderDetail);
            // set the owning side to null (unless already changed)
            if ($firstResponderDetail->getOrganization() === $this) {
                $firstResponderDetail->setOrganization(null);
            }
        }

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
     * @return Organization
     */
    public function addAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if (!$this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails[] = $advocateDetail;
            $advocateDetail->setOrganization($this);
        }

        return $this;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return Organization
     */
    public function removeAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if ($this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails->removeElement($advocateDetail);
            // set the owning side to null (unless already changed)
            if ($advocateDetail->getOrganization() === $this) {
                $advocateDetail->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * @param float|null $lat
     * @return Organization
     */
    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLng(): ?float
    {
        return $this->lng;
    }

    /**
     * @param float|null $lng
     * @return Organization
     */
    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return Collection|ClientType[]
     */
    public function getClientTypes(): Collection
    {
        return $this->clientTypes;
    }

    /**
     * @param ClientType $clientType
     * @return $this
     */
    public function addClientType(ClientType $clientType): self
    {
        if (!$this->clientTypes->contains($clientType)) {
            $this->clientTypes[] = $clientType;
        }

        return $this;
    }

    /**
     * @param ClientType $clientType
     * @return $this
     */
    public function removeClientType(ClientType $clientType): self
    {
        if ($this->clientTypes->contains($clientType)) {
            $this->clientTypes->removeElement($clientType);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return $this
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     * @return $this
     */
    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
