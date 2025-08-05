<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DependentRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Dependent extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clothingSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shoeSize;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientDetail", inversedBy="dependents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientDetail;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return Dependent
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
     * @return Dependent
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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
     * @return Dependent
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     * @return Dependent
     */
    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

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
     * @return Dependent
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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
     * @return Dependent
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
     * @return Dependent
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
     * @return Dependent
     */
    public function setShoeSize(?string $shoeSize): self
    {
        $this->shoeSize = $shoeSize;

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
     * @param ClientDetail|null $clientDetail
     * @return Dependent
     */
    public function setClientDetail(?ClientDetail $clientDetail): self
    {
        $this->clientDetail = $clientDetail;
        return $this;
    }
}
