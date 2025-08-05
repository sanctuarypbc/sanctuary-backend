<?php

namespace App\ApiBundle\Service;

use App\Entity\Facility;
use App\Entity\FacilityType;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\FacilityRepository;
use App\Repository\FacilityTypeRepository;

/**
 * Class FacilityTypeService
 * @package App\ApiBundle\Service
 */
class FacilityTypeService
{
    /** @var FacilityTypeRepository  */
    private $facilityTypeRepository;

    /** @var FacilityRepository  */
    private $facilityRepository;

    /**
     * FacilityTypeService constructor.
     * @param FacilityTypeRepository $facilityTypeRepository
     * @param FacilityRepository $facilityRepository
     */
    public function __construct(FacilityTypeRepository $facilityTypeRepository, FacilityRepository $facilityRepository)
    {
        $this->facilityTypeRepository = $facilityTypeRepository;
        $this->facilityRepository = $facilityRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\FacilityType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addFacilityType($name)
    {
        return $this->facilityTypeRepository->addFacilityType($name);
    }

    /**
     * @param $facilityTypeId
     * @param $name
     */
    public function updateFacilityTypeById($facilityTypeId, $name)
    {
        return $this->facilityTypeRepository->updateFacilityTypeById($facilityTypeId, $name);
    }

    /**
     * @param int $facilityTypeId
     * @return bool|void
     */
    public function deleteFacilityTypeById($facilityTypeId)
    {
        $linkedFacility = $this->facilityRepository
            ->findOneBy(['status' => StatusEnum::ACTIVE, 'facilityType' => $facilityTypeId]);
        if ($linkedFacility instanceof Facility) {
            return false;
        }

        return $this->facilityTypeRepository->deleteFacilityTypeById($facilityTypeId);
    }

    /**
     * @param $id
     * @return FacilityType|null
     */
    public function getFacilityTypeById($id)
    {
        return $this->facilityTypeRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFacilityTypeLikeName($name)
    {
        return $this->facilityTypeRepository->getFacilityTypeLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return FacilityType|null
     */
    public function getFacilityTypeByIdAndName($id, $name)
    {
        return $this->facilityTypeRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $facilityTypeId
     * @param $name
     * @return array
     */
    public function getFacilityTypeList($facilityTypeId, $name)
    {
        $response = [];
        if ($facilityTypeId || $name) {
            $facilityType = empty($name) ? $this->getFacilityTypeById($facilityTypeId) : (
                empty($facilityTypeId) ? $this->getFacilityTypeLikeName($name) : $this->getFacilityTypeByIdAndName($facilityTypeId, $name)
            );
            if (!empty($facilityType) && is_array($facilityType)) {
                foreach ($facilityType as $item) {
                    $response[] = $this->makeSingleTypeResponse($item);

                }
            } elseif (!empty($facilityType)) {
                $response = $this->makeSingleTypeResponse($facilityType);

            }
            return $response;
        }

        $facilityTypes = $this->facilityTypeRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($facilityTypes as $facilityType) {
            $response[] = $this->makeSingleTypeResponse($facilityType);
        }
        return $response;
    }


    /**
     * @param FacilityType $facilityType
     * @return array
     */
    public function makeSingleTypeResponse(FacilityType $facilityType)
    {
        $singleData = [];
        $singleData['id'] = $facilityType->getId();
        $singleData['name'] = $facilityType->getName();
        $singleData['status'] = $facilityType->getStatus();
        $singleData['created_on'] = $facilityType->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
