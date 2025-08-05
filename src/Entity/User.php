<?php

namespace App\Entity;

use ActivityLogBundle\Entity\Interfaces\StringableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 *
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(
 *         name="email",
 *         column=@ORM\Column(nullable=true)
 *     ),
 *     @ORM\AttributeOverride(
 *         name="emailCanonical",
 *         column=@ORM\Column(nullable=true, unique=false)
 *     )
 * })
 *
 */
class User extends BaseUser implements StringableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FirstResponderDetail", mappedBy="user", cascade={"persist", "remove"})
     */
    private $firstResponderDetail;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AdvocateDetail", mappedBy="user", cascade={"persist", "remove"})
     */
    private $advocateDetail;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ClientDetail", mappedBy="user", cascade={"persist", "remove"})
     */
    private $clientDetail;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Facility", mappedBy="user", cascade={"persist", "remove"})
     */
    private $facility;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Facility", inversedBy="waitListedUsers", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="facility_waitlist")
     * @ORM\JoinColumn(name="facility_waitlist", nullable=false)
     */
    private $waitListedFacility;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserAddress", mappedBy="user", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResetToken", mappedBy="user")
     */
    private $resetTokens;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verificationCode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verificationRequestedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $agreedTerms;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $agreedTermsAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->resetTokens = new ArrayCollection();
        $this->waitListedFacility = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'roles' => $this->roles
        ];
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return User
     */
    public function setCreated(\DateTime $created): User
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated(\DateTime $updated): User
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return FirstResponderDetail|null
     */
    public function getFirstResponderDetail(): ?FirstResponderDetail
    {
        return $this->firstResponderDetail;
    }

    /**
     * @param FirstResponderDetail $firstResponderDetail
     * @return User
     */
    public function setFirstResponderDetail(FirstResponderDetail $firstResponderDetail): self
    {
        $this->firstResponderDetail = $firstResponderDetail;

        // set the owning side of the relation if necessary
        if ($firstResponderDetail->getUser() !== $this) {
            $firstResponderDetail->setUser($this);
        }

        return $this;
    }

    /**
     * @return AdvocateDetail|null
     */
    public function getAdvocateDetail(): ?AdvocateDetail
    {
        return $this->advocateDetail;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return User
     */
    public function setAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        $this->advocateDetail = $advocateDetail;

        // set the owning side of the relation if necessary
        if ($advocateDetail->getUser() !== $this) {
            $advocateDetail->setUser($this);
        }

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
     * @param ClientDetail $clientDetail
     * @return User
     */
    public function setClientDetail(ClientDetail $clientDetail): self
    {
        $this->clientDetail = $clientDetail;

        // set the owning side of the relation if necessary
        if ($clientDetail->getUser() !== $this) {
            $clientDetail->setUser($this);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return User
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDob(): ?string
    {
        return $this->dob;
    }

    /**
     * @param string|null $dob
     * @return User
     */
    public function setDob(?string $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     * @return User
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|ResetToken[]
     */
    public function getResetTokens(): Collection
    {
        return $this->resetTokens;
    }

    /**
     * @param ResetToken $resetToken
     * @return User
     */
    public function addResetToken(ResetToken $resetToken): self
    {
        if (!$this->resetTokens->contains($resetToken)) {
            $this->resetTokens[] = $resetToken;
            $resetToken->setUser($this);
        }

        return $this;
    }

    /**
     * @param ResetToken $resetToken
     * @return User
     */
    public function removeResetToken(ResetToken $resetToken): self
    {
        if ($this->resetTokens->contains($resetToken)) {
            $this->resetTokens->removeElement($resetToken);
            // set the owning side to null (unless already changed)
            if ($resetToken->getUser() === $this) {
                $resetToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return User
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return User
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName() : ?string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
     * @return Facility|null
     */
    public function getFacility(): ?Facility
    {
        return $this->facility;
    }

    /**
     * @param Facility $facility
     * @return $this
     */
    public function setFacility(Facility $facility): self
    {
        $this->facility = $facility;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    /**
     * @param string|null $verificationCode
     * @return $this
     */
    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getVerificationRequestedAt(): ?\DateTimeInterface
    {
        return $this->verificationRequestedAt;
    }

    /**
     * @param \DateTimeInterface|null $verificationRequestedAt
     * @return $this
     */
    public function setVerificationRequestedAt(?\DateTimeInterface $verificationRequestedAt): self
    {
        $this->verificationRequestedAt = $verificationRequestedAt;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAgreedTerms(): ?bool
    {
        return $this->agreedTerms;
    }

    /**
     * @param bool|null $agreedTerms
     * @return $this
     */
    public function setAgreedTerms(?bool $agreedTerms): self
    {
        $this->agreedTerms = $agreedTerms;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getAgreedTermsAt(): ?\DateTimeInterface
    {
        return $this->agreedTermsAt;
    }

    /**
     * @param \DateTimeInterface|null $agreedTermsAt
     * @return $this
     */
    public function setAgreedTermsAt(?\DateTimeInterface $agreedTermsAt): self
    {
        $this->agreedTermsAt = $agreedTermsAt;

        return $this;
    }

    /**
     * @return mixed|UserAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getWaitListedFacility()
    {
        return $this->waitListedFacility;
    }

    /**
     * @param ArrayCollection $waitListedFacility
     */
    public function setWaitListedFacility(ArrayCollection $waitListedFacility): void
    {
        $this->waitListedFacility = $waitListedFacility;
    }

    /**
     * @param Facility $facility
     * @return $this
     */
    public function addWaitListedFacility(Facility $facility): self
    {
        if (!$this->waitListedFacility->contains($facility)) {
            $this->waitListedFacility[] = $facility;
        }
        return $this;
    }

    /**
     * @param Facility $facility
     * @return $this
     */
    public function removeWaitListedFacility(Facility $facility): self
    {
        if ($this->waitListedFacility->contains($facility)) {
            $this->waitListedFacility->removeElement($facility);
        }
        return $this;
    }
}
