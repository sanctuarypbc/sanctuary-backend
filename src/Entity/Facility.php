<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacilityRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Facility extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=false})
     */
    private $availableBeds;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dependentsAllowed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $petsAllowed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hoursOfOperation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FacilityType", inversedBy="facilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $facilityType;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zipCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClientDetail", mappedBy="facility")
     */
    private $clientDetails;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="facility", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="waitListedFacility", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $waitListedUsers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $openingTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $closingTime;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $workAllDay;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primaryColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $secondaryColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlPrefix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $desktopLogo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobileLogo;

    /**
     * Facility constructor.
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
     * @return Facility
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return Facility
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAvailableBeds(): ?bool
    {
        return $this->availableBeds;
    }

    /**
     * @param bool|null $availableBeds
     * @return Facility
     */
    public function setAvailableBeds(?bool $availableBeds): self
    {
        $this->availableBeds = $availableBeds;

        return $this;
    }

    /**
     * @return int
     */
    public function getDependentsAllowed(): ?int
    {
        return $this->dependentsAllowed;
    }

    /**
     * @param int|null $dependentsAllowed
     * @return Facility
     */
    public function setDependentsAllowed(?int $dependentsAllowed): self
    {
        $this->dependentsAllowed = $dependentsAllowed;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPetsAllowed(): ?bool
    {
        return $this->petsAllowed;
    }

    /**
     * @param bool|null $petsAllowed
     * @return Facility
     */
    public function setPetsAllowed(?bool $petsAllowed): self
    {
        $this->petsAllowed = $petsAllowed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHoursOfOperation(): ?string
    {
        return $this->hoursOfOperation;
    }

    /**
     * @param string|null $hoursOfOperation
     * @return Facility
     */
    public function setHoursOfOperation(?string $hoursOfOperation): self
    {
        $this->hoursOfOperation = $hoursOfOperation;

        return $this;
    }

    /**
     * @return FacilityType|null
     */
    public function getFacilityType(): ?FacilityType
    {
        return $this->facilityType;
    }

    /**
     * @param FacilityType|null $FacilityType
     * @return Facility
     */
    public function setFacilityType(?FacilityType $FacilityType): self
    {
        $this->facilityType = $FacilityType;

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
     * @return Facility
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
     * @return Facility
     */
    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

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
     * @return Facility
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

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
     * @return Facility
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

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
     * @return Facility
     */
    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

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
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Facility
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }

    /**
     * @param $openingTime
     * @return Facility
     */
    public function setOpeningTime($openingTime): self
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }

    /**
     * @param $closingTime
     * @return Facility
     */
    public function setClosingTime($closingTime): self
    {
        $this->closingTime = $closingTime;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWorkAllDay()
    {
        return $this->workAllDay;
    }

    /**
     * @param bool|null $workAllDay
     * @return Facility
     */
    public function setWorkAllDay(?bool $workAllDay): self
    {
        $this->workAllDay = $workAllDay;

        return $this;
    }

    /**
     * @param ClientDetail $clientDetail
     * @return Facility
     */
    public function addClientDetail(ClientDetail $clientDetail): self
    {
        if (!$this->clientDetails->contains($clientDetail)) {
            $this->clientDetails[] = $clientDetail;
            $clientDetail->setFacility($this);
        }

        return $this;
    }

    /**
     * @param ClientDetail $clientDetail
     * @return Facility
     */
    public function removeClientDetail(ClientDetail $clientDetail): self
    {
        if ($this->clientDetails->contains($clientDetail)) {
            $this->clientDetails->removeElement($clientDetail);
            // set the owning side to null (unless already changed)
            if ($clientDetail->getFacility() === $this) {
                $clientDetail->setFacility(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    /**
     * @param string|null $primaryColor
     * @return $this
     */
    public function setPrimaryColor(?string $primaryColor): self
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    /**
     * @param string|null $secondaryColor
     * @return $this
     */
    public function setSecondaryColor(?string $secondaryColor): self
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlPrefix(): ?string
    {
        return $this->urlPrefix;
    }

    /**
     * @param string|null $urlPrefix
     * @return $this
     */
    public function setUrlPrefix(?string $urlPrefix): self
    {
        $this->urlPrefix = $urlPrefix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDesktopLogo(): ?string
    {
        return $this->desktopLogo;
    }

    /**
     * @param string|null $desktopLogo
     * @return $this
     */
    public function setDesktopLogo(?string $desktopLogo): self
    {
        $this->desktopLogo = $desktopLogo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobileLogo(): ?string
    {
        return $this->mobileLogo;
    }

    /**
     * @param string|null $mobileLogo
     * @return $this
     */
    public function setMobileLogo(?string $mobileLogo): self
    {
        $this->mobileLogo = $mobileLogo;

        return $this;
    }
}
