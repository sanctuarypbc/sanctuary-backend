<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserAddress;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddress[]    findAll()
 * @method UserAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressRepository extends AbstractRepository
{
    /**
     * UserAddressRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    /**
     * @param User $user
     * @param $data
     * @return array|bool[]
     */
    public function updateUserAddressFromData(User $user, $data)
    {
        $userAddress = $user->getAddress();
        if (empty($userAddress)) {
            return $this->createUserAddressFromData($user, $data);
        }

        if (isset($data['street_address'])) {
            $userAddress->setStreetAddress($data['street_address']);
        }
        if (isset($data['city'])) {
            $userAddress->setCity($data['city']);
        }
        if (isset($data['state'])) {
            $userAddress->setState($data['state']);
        }
        if (isset($data['zip'])) {
            $userAddress->setZip($data['zip']);
        }
        if (isset($data['is_current_address'])) {
            $userAddress->setAddressState($data['is_current_address'] ? UserAddress::ADDRESS_STATE_CURRENT : UserAddress::ADDRESS_STATE_FORMER);
        }
        if (isset($data['is_apartment'])) {
            $userAddress->setIsApartment($data['is_apartment']);
        }
        if (isset($data['is_apartment']) && $data['is_apartment'] && isset($data['apartment_unit_number'])) {
            $userAddress->setApartmentUnitNumber($data['apartment_unit_number']);
        } elseif (isset($data['is_apartment'])) {
            return ["status" => false, "message" => "Apartment Unit Number is missing"];
        }

        return ["status" => true];
    }

    /**
     * @param User $user
     * @param $data
     * @return array|bool[]
     */
    public function createUserAddressFromData(User $user, $data)
    {
        try {
            $userAddress = new UserAddress();
            $currentAddress = UserAddress::ADDRESS_STATE_FORMER;
            if (isset($data['is_current_address'])) {
                $currentAddress =  $data['is_current_address'] ? UserAddress::ADDRESS_STATE_CURRENT : UserAddress::ADDRESS_STATE_FORMER;
            }
            $userAddress->setStreetAddress(isset($data['street_address']) ? $data['street_address'] : null);
            $userAddress->setCity(isset($data['city']) ? $data['city'] : null);
            $userAddress->setState(isset($data['state']) ? $data['state'] : null);
            $userAddress->setZip(isset($data['zip']) ? $data['zip'] : null);
            $userAddress->setAddressState($currentAddress);
            $userAddress->setIsApartment(isset($data['is_apartment']) ? $data['is_apartment'] : 0);
            $userAddress->setApartmentUnitNumber(isset($data['apartment_unit_number']) ? $data['apartment_unit_number'] : null);
            $userAddress->setUser($user);
            $this->_em->persist($userAddress);
        } catch (\Exception $exception) {
            return ["status" => false, "message" =>  "Something went wrong"];
        }

        return ["status" => true];
    }
}