<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FirstResponderDetailRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class FirstResponderDetail extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officePhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identificationNumber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="firstResponderDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="firstResponderDetails")
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FirstResponderType", inversedBy="firstResponderDetails")
     */
    private $firstResponderType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClientDetail", mappedBy="firstResponder")
     */
    private $clientDetails;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'nick_name' => $this->nickName,
            'office_phone' => $this->officePhone,
            'identification_number' => $this->identificationNumber
        ];
    }

    /**
     * @return string|null
     */
    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    /**
     * @param string|null $officePhone
     * @return FirstResponderDetail
     */
    public function setOfficePhone(?string $officePhone): self
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    /**
     * @param string|null $identificationNumber
     * @return FirstResponderDetail
     */
    public function setIdentificationNumber(?string $identificationNumber): self
    {
        $this->identificationNumber = $identificationNumber;

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
     * @return FirstResponderDetail
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

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
     * @return FirstResponderDetail
     */
    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return FirstResponderType|null
     */
    public function getFirstResponderType(): ?FirstResponderType
    {
        return $this->firstResponderType;
    }

    /**
     * @param FirstResponderType|null $firstResponderType
     * @return $this
     */
    public function setFirstResponderType(?FirstResponderType $firstResponderType): self
    {
        $this->firstResponderType = $firstResponderType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    /**
     * @param string|null $nickName
     * @return $this
     */
    public function setNickName(?string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }
}
