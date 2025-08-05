<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientDetailRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientDetail extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $contactPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $safeLocation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $emergencyContact;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $employmentStatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $location;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     */
    private $petStatus;

    /**
     * @ORM\Column(type="integer", length=11, nullable=true)
     * @Gedmo\Versioned
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $clothingSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $shoeSize;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FirstResponderDetail", inversedBy="clientDetails")
     */
    private $firstResponder;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CovidAnswer", mappedBy="clientDetail")
     *
     */
    private $covidAnswers;

    /**
     * @return mixed
     */
    public function getCovidAnswers()
    {
        return $this->covidAnswers;
    }

    /**
     * @param mixed $covidAnswers
     */
    public function setCovidAnswers($covidAnswers): void
    {
        $this->covidAnswers = $covidAnswers;
    }


    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $dateAssisted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdvocateDetail", inversedBy="clientDetails")
     * @Gedmo\Versioned
     */
    private $advocate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $dateAssigned;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="clientDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientType", inversedBy="clientDetails")
     * @Gedmo\Versioned
     */
    private $clientType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientStatus", inversedBy="clientDetails")
     * @Gedmo\Versioned
     */
    private $clientStatus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientOccupation", inversedBy="clientDetails")
     * @Gedmo\Versioned
     */
    private $clientOccupation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned
     */
    private $totalDependents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Facility", inversedBy="clientDetails")
     * @Gedmo\Versioned
     */
    private $facility;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Dependent", mappedBy="clientDetail")
     */
    private $dependents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientEmploymentStatus", inversedBy="clientDetail")
     */
    private $clientEmploymentStatus;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $phoneWithCellularService;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $needTranslator;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caseNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfIncident;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $abuserLocation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfPets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $physicallyDisabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $needMedicalAssistance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $contactedFamily;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWaitlisted;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Gedmo\Versioned
     */
    private $race;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Gedmo\Versioned
     */
    private $ethnicity;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Gedmo\Versioned
     */
    private $incidentZipCode;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'client_detail_id' => $this->id,
            'age' => $this->age,
            'total_dependents' => $this->totalDependents,
            'pet_status' => $this->petStatus,
            'phone_with_cellular_service' => $this->phoneWithCellularService,
            'need_translator' => $this->needTranslator,
            'case_number' => $this->caseNumber,
            'date_of_incident' => $this->dateOfIncident,
            'valid_id' => $this->validId,
            'abuser_location' => $this->abuserLocation,
            'number_of_pets' => $this->numberOfPets,
            'physically_disabled' => $this->physicallyDisabled,
            'need_medical_assistance' => $this->needMedicalAssistance,
            'contacted_family' => $this->contactedFamily,
            'is_waitlisted' => $this->isWaitlisted,
            'notes' => $this->notes,
            'location' =>$this->location,
            'race' => $this->race,
            'ethnicity' => $this->ethnicity,
            'incident_zip_code' => $this->incidentZipCode,
        ];
    }

    /**
     * @return string|nullboolean
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     * @return ClientDetail
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

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
     * @return ClientDetail
     */
    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSafeLocation(): ?string
    {
        return $this->safeLocation;
    }

    /**
     * @param string|null $safeLocation
     * @return ClientDetail
     */
    public function setSafeLocation(?string $safeLocation): self
    {
        $this->safeLocation = $safeLocation;

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
     * @return ClientDetail
     */
    public function setEmergencyContact(?string $emergencyContact): self
    {
        $this->emergencyContact = $emergencyContact;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEmploymentStatus(): ?bool
    {
        return $this->employmentStatus;
    }

    /**
     * @param bool|null $employmentStatus
     * @return ClientDetail
     */
    public function setEmploymentStatus(?bool $employmentStatus): self
    {
        $this->employmentStatus = $employmentStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return ClientDetail
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPetStatus(): ?string
    {
        return $this->petStatus;
    }

    /**
     * @param string|null $petStatus
     * @return ClientDetail
     */
    public function setPetStatus(?string $petStatus): self
    {
        $this->petStatus = $petStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAge(): ?string
    {
        return $this->age;
    }

    /**
     * @param string|null $age
     * @return ClientDetail
     */
    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClothingSize(): ?string
    {
        return $this->clothingSize;
    }

    /**
     * @param string|null $clothingSize
     * @return ClientDetail
     */
    public function setClothingSize(?string $clothingSize): self
    {
        $this->clothingSize = $clothingSize;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShoeSize(): ?string
    {
        return $this->shoeSize;
    }

    /**
     * @param string|null $shoeSize
     * @return ClientDetail
     */
    public function setShoeSize(?string $shoeSize): self
    {
        $this->shoeSize = $shoeSize;

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
     * @return ClientDetail
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return FirstResponderDetail|null
     */
    public function getFirstResponder(): ?FirstResponderDetail
    {
        return $this->firstResponder;
    }

    /**
     * @param FirstResponderDetail|null $firstResponder
     * @return ClientDetail
     */
    public function setFirstResponder(?FirstResponderDetail $firstResponder): self
    {
        $this->firstResponder = $firstResponder;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateAssisted(): ?\DateTimeInterface
    {
        return $this->dateAssisted;
    }

    /**
     * @param \DateTimeInterface|null $dateAssisted
     * @return ClientDetail
     */
    public function setDateAssisted(?\DateTimeInterface $dateAssisted): self
    {
        $this->dateAssisted = $dateAssisted;

        return $this;
    }

    /**
     * @return AdvocateDetail|null
     */
    public function getAdvocate(): ?AdvocateDetail
    {
        return $this->advocate;
    }

    /**
     * @param AdvocateDetail|null $advocate
     * @return ClientDetail
     */
    public function setAdvocate(?AdvocateDetail $advocate): self
    {
        $this->advocate = $advocate;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateAssigned(): ?\DateTimeInterface
    {
        return $this->dateAssigned;
    }

    /**
     * @param \DateTimeInterface|null $dateAssigned
     * @return ClientDetail
     */
    public function setDateAssigned(?\DateTimeInterface $dateAssigned): self
    {
        $this->dateAssigned = $dateAssigned;

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
     * @return ClientDetail
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ClientType|null
     */
    public function getClientType(): ?ClientType
    {
        return $this->clientType;
    }

    /**
     * @param ClientType|null $clientType
     * @return ClientDetail
     */
    public function setClientType(?ClientType $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    /**
     * @return ClientStatus|null
     */
    public function getClientStatus(): ?ClientStatus
    {
        return $this->clientStatus;
    }

    /**
     * @param ClientStatus|null $clientStatus
     * @return ClientDetail
     */
    public function setClientStatus(?ClientStatus $clientStatus): self
    {
        $this->clientStatus = $clientStatus;

        return $this;
    }

    /**
     * @return ClientOccupation|null
     */
    public function getClientOccupation(): ?ClientOccupation
    {
        return $this->clientOccupation;
    }

    /**
     * @param ClientOccupation|null $clientOccupation
     * @return ClientDetail
     */
    public function setClientOccupation(?ClientOccupation $clientOccupation): self
    {
        $this->clientOccupation = $clientOccupation;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalDependents(): ?int
    {
        return $this->totalDependents;
    }

    /**
     * @param int|null $totalDependents
     * @return ClientDetail
     */
    public function setTotalDependents(?int $totalDependents): self
    {
        $this->totalDependents = $totalDependents;

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
     * @return ClientDetail
     */
    public function setFacility(?Facility $facility): self
    {
        $this->facility = $facility;

        return $this;
    }

    /**
     * @return Dependent|null
     */
    public function getDependents(): ?Dependent
    {
        return $this->dependents;
    }

    /**
     * @param Dependent|null $dependents
     * @return ClientDetail
     */
    public function setDependents(?Dependent $dependents): self
    {
        $this->dependents = $dependents;
        return $this;
    }

    public function getClientEmploymentStatus(): ?ClientEmploymentStatus
    {
        return $this->clientEmploymentStatus;
    }

    public function setClientEmploymentStatus(?ClientEmploymentStatus $clientEmploymentStatus): self
    {
        $this->clientEmploymentStatus = $clientEmploymentStatus;

        return $this;
    }

    public function getPhoneWithCellularService(): ?bool
    {
        return $this->phoneWithCellularService;
    }

    public function setPhoneWithCellularService(?bool $phoneWithCellularService): self
    {
        $this->phoneWithCellularService = $phoneWithCellularService;

        return $this;
    }

    public function getNeedTranslator(): ?bool
    {
        return $this->needTranslator;
    }

    public function setNeedTranslator(?bool $needTranslator): self
    {
        $this->needTranslator = $needTranslator;

        return $this;
    }

    public function getCaseNumber(): ?string
    {
        return $this->caseNumber;
    }

    public function setCaseNumber(?string $caseNumber): self
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }

    public function getDateOfIncident(): ?\DateTimeInterface
    {
        return $this->dateOfIncident;
    }

    public function setDateOfIncident(?\DateTimeInterface $dateOfIncident): self
    {
        $this->dateOfIncident = $dateOfIncident;

        return $this;
    }

    public function getValidId(): ?bool
    {
        return $this->validId;
    }

    public function setValidId(?bool $validId): self
    {
        $this->validId = $validId;

        return $this;
    }

    public function getAbuserLocation(): ?string
    {
        return $this->abuserLocation;
    }

    public function setAbuserLocation(?string $abuserLocation): self
    {
        $this->abuserLocation = $abuserLocation;

        return $this;
    }

    public function getNumberOfPets(): ?int
    {
        return $this->numberOfPets;
    }

    public function setNumberOfPets(?int $numberOfPets): self
    {
        $this->numberOfPets = $numberOfPets;

        return $this;
    }

    public function getPhysicallyDisabled(): ?string
    {
        return $this->physicallyDisabled;
    }

    public function setPhysicallyDisabled(?string $physicallyDisabled): self
    {
        $this->physicallyDisabled = $physicallyDisabled;

        return $this;
    }

    public function getNeedMedicalAssistance(): ?bool
    {
        return $this->needMedicalAssistance;
    }

    public function setNeedMedicalAssistance(?bool $needMedicalAssistance): self
    {
        $this->needMedicalAssistance = $needMedicalAssistance;

        return $this;
    }

    public function getContactedFamily(): ?bool
    {
        return $this->contactedFamily;
    }

    public function setContactedFamily(?bool $contactedFamily): self
    {
        $this->contactedFamily = $contactedFamily;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientAddress()
    {
        return $this->clientAddress;
    }

    /**
     * @param mixed $clientAddress
     */
    public function setClientAddress($clientAddress): void
    {
        $this->clientAddress = $clientAddress;
    }

    /**
     * @return mixed
     */
    public function getIsWaitlisted()
    {
        return $this->isWaitlisted;
    }

    /**
     * @param mixed $isWaitlisted
     */
    public function setIsWaitlisted($isWaitlisted): void
    {
        $this->isWaitlisted = $isWaitlisted;
    }

    /**
     * @return string
     */
    public function getRace(): ?string
    {
        return $this->race;
    }

    /**
     * @param $race
     * @return ClientDetail
     */
    public function setRace($race): self
    {
        $this->race = $race;
        return $this;
    }

    /**
     * @return string
     */
    public function getEthnicity(): ?string
    {
        return $this->ethnicity;
    }

    /**
     * @param $ethnicity
     * @return ClientDetail
     */
    public function setEthnicity($ethnicity): self
    {
        $this->ethnicity = $ethnicity;
        return $this;
    }

    /**
     * @return string
     */
    public function getIncidentZipCode(): ?string
    {
        return $this->incidentZipCode;
    }

    /**
     * @param $incidentZipCode
     * @return ClientDetail
     */
    public function setIncidentZipCode($incidentZipCode): self
    {
        $this->incidentZipCode = $incidentZipCode;
        return $this;
    }
}
