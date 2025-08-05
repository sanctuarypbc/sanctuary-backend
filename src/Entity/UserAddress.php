<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_address")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class UserAddress extends AbstractEntity
{
    const ADDRESS_STATE_CURRENT = 0;
    const ADDRESS_STATE_FORMER = 1;
    const APARTMENT_ADDRESS = 0;
    const NOT_APARTMENT_ADDRESS = 1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $streetAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isApartment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $apartmentUnitNumber;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $addressState; // current or former

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @return mixed
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @param mixed $streetAddress
     */
    public function setStreetAddress($streetAddress): void
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getIsApartment()
    {
        return $this->isApartment;
    }

    /**
     * @param mixed $isApartment
     */
    public function setIsApartment($isApartment): void
    {
        $this->isApartment = $isApartment;
    }

    /**
     * @return mixed
     */
    public function getApartmentUnitNumber()
    {
        return $this->apartmentUnitNumber;
    }

    /**
     * @param mixed $apartmentUnitNumber
     */
    public function setApartmentUnitNumber($apartmentUnitNumber): void
    {
        $this->apartmentUnitNumber = $apartmentUnitNumber;
    }

    /**
     * @return mixed
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * @param mixed $addressState
     */
    public function setAddressState($addressState): void
    {
        $this->addressState = $addressState;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}