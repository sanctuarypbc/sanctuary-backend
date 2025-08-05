<?php

namespace App\ApiBundle\Service;

use App\Entity\Facility;
use App\Entity\FacilityInventoryType;
use App\Entity\User;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\FacilityInventoryRepository;
use App\Repository\FacilityInventoryTypeRepository;
use Symfony\Component\Security\Core\Security;

/**
 * Class FacilityInventoryTypeService
 * @package App\ApiBundle\Service
 */
class FacilityInventoryTypeService
{
    /** @var UtilService  */
    private $utilService;

    /** @var FacilityService */
    private $facilityService;

    /** @var FacilityInventoryTypeRepository */
    private $inventoryTypeRepository;

    /** @var User  */
    private $user;

    /** @var FacilityInventoryRepository  */
    private $facilityInventoryRepository;

    /**
     * FacilityInventoryTypeService constructor.
     * @param UtilService $utilService
     * @param FacilityService $facilityService
     * @param FacilityInventoryTypeRepository $facilityInventoryTypeRepository
     * @param Security $security
     * @param FacilityInventoryRepository $facilityInventoryRepository
     */
    public function __construct(
        UtilService $utilService,
        FacilityService $facilityService,
        FacilityInventoryTypeRepository $facilityInventoryTypeRepository,
        Security $security,
        FacilityInventoryRepository $facilityInventoryRepository
    ) {
        $this->utilService = $utilService;
        $this->facilityService = $facilityService;
        $this->inventoryTypeRepository = $facilityInventoryTypeRepository;
        $this->user = $security->getUser();
        $this->facilityInventoryRepository = $facilityInventoryRepository;
    }

    /**
     * @param int|null $id
     * @param bool $getInventoryCount
     * @param int|null $facilityUserId
     * @return array|bool|null
     */
    public function getInventoryTypes($id = null, $getInventoryCount = false, $facilityUserId = null)
    {
        if (in_array(UserEnum::ROLE_SUPER_ADMIN, $this->user->getRoles())) {
            $facility = $this->facilityService->getFacilityByUserId($facilityUserId);
        } else {
            $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
        }

        // Check if provided facility id is valid
        if (!$facility instanceof Facility) {
            return false;
        }

        return $this->inventoryTypeRepository->getInventoryTypes($facility, $id, $getInventoryCount);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createInventoryType($name)
    {
        $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
        if (empty($facility)) {
            return false;
        }

        $this->inventoryTypeRepository->create($facility, $name);
        return true;
    }

    /**
     * @param int $id
     * @param string $name
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateInventoryType($id, $name)
    {
        $inventoryType = $this->getInventoryType($id);

        if (!$inventoryType instanceof FacilityInventoryType) {
            return false;
        }

        $inventoryType->setName($name);
        $this->inventoryTypeRepository->flush();

        return true;
    }

    /**
     * @param int $id
     * @param bool $checkFacility
     * @return object|null
     */
    public function getInventoryType($id, $checkFacility = true)
    {
        $filter = ['id' => $id, 'status' => StatusEnum::ACTIVE];
        if ($checkFacility) {
            $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
            if (!$facility instanceof Facility) {
                return null;
            }
            $filter['facility'] = $facility->getId();
        }

        return $this->inventoryTypeRepository->findOneBy($filter);
    }

    /**
     * @param $id
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteInventoryType($id)
    {
        $inventoryType = $this->getInventoryType($id);

        if (!$inventoryType instanceof FacilityInventoryType) {
            return [
                'status' => false,
                'message' => 'Either inventory type does not exist or you don\'t have sufficient rights to delete this item.'
            ];
        }

        if ($this->facilityInventoryRepository->checkIfInventoryTypeLinked($inventoryType)) {
            return ['status' => false, 'message' => 'Inventory type is linked with an Inventory.'];
        }

        $inventoryType->setStatus(StatusEnum::INACTIVE);
        $this->inventoryTypeRepository->flush();

        return ['status' => true];
    }


    /**
     * @param Facility $facility
     * @param $facilityInventoryType
     * @return object|null
     */
    public function getFacilityInventoryTypeByFacility(Facility $facility, $facilityInventoryType)
    {
         return $this->inventoryTypeRepository->findOneBy(["id" => $facilityInventoryType, "facility" => $facility]);
    }

}