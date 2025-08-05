<?php

namespace App\ApiBundle\Service;

use App\Entity\AdvocateDetail;
use App\Entity\AdvocateServiceType;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\AdvocateDetailRepository;
use App\Repository\AdvocateServiceTypeRepository;

/**
 * Class AdvocateServiceTypeService
 * @package App\ApiBundle\Service
 */
class AdvocateServiceTypeService
{
    /** @var AdvocateServiceTypeRepository  */
    private $advocateServiceTypeRepository;

    /** @var AdvocateDetailRepository  */
    private $advocateDetailRepository;

    /**
     * AdvocateServiceTypeService constructor.
     * @param AdvocateServiceTypeRepository $advocateServiceTypeRepository
     * @param AdvocateDetailRepository $advocateDetailRepository
     */
    public function __construct(
        AdvocateServiceTypeRepository $advocateServiceTypeRepository,
        AdvocateDetailRepository $advocateDetailRepository
    ) {
        $this->advocateServiceTypeRepository = $advocateServiceTypeRepository;
        $this->advocateDetailRepository = $advocateDetailRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\AdvocateServiceType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAdvocateServiceType($name)
    {
        return $this->advocateServiceTypeRepository->addAdvocateServiceType($name);
    }

    /**
     * @param $advocateServiceTypeId
     * @param $name
     */
    public function updateAdvocateServiceTypeById($advocateServiceTypeId, $name)
    {
        return $this->advocateServiceTypeRepository->updateAdvocateServiceTypeById($advocateServiceTypeId, $name);
    }

    /**
     * @param int $advocateServiceTypeId
     * @return bool|void
     */
    public function deleteAdvocateServiceTypeById($advocateServiceTypeId)
    {
        $linkedAdvocate = $this->advocateDetailRepository
            ->findOneBy(['status' => StatusEnum::ACTIVE, 'serviceType' => $advocateServiceTypeId]);
        if ($linkedAdvocate instanceof AdvocateDetail) {
            return false;
        }

        return $this->advocateServiceTypeRepository->deleteAdvocateServiceTypeById($advocateServiceTypeId);
    }

    /**
     * @param $id
     * @return AdvocateServiceType|null
     */
    public function getAdvocateServiceTypeById($id)
    {
        return $this->advocateServiceTypeRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getAdvocateServiceTypeLikeName($name)
    {
        return $this->advocateServiceTypeRepository->getAdvocateServiceTypeLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return AdvocateServiceType|null
     */
    public function getAdvocateServiceTypeByIdAndName($id, $name)
    {
        return $this->advocateServiceTypeRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $advocateServiceTypeId
     * @param $name
     * @return array
     */
    public function getAdvocateServiceTypeList($advocateServiceTypeId, $name)
    {
        $response = [];
        if ($advocateServiceTypeId || $name) {
            $advocateServiceType = empty($name) ? $this->getAdvocateServiceTypeById($advocateServiceTypeId) : (
                empty($advocateServiceTypeId) ? $this->getAdvocateServiceTypeLikeName($name) : $this->getAdvocateServiceTypeByIdAndName($advocateServiceTypeId, $name)
            );
            if (!empty($advocateServiceType) && is_array($advocateServiceType)) {
                foreach ($advocateServiceType as $item) {
                    $response[] = $this->makeSingleTypeResponse($item);
                }
            } elseif (!empty($advocateServiceType)) {
                $response = $this->makeSingleTypeResponse($advocateServiceType);

            }
            return $response;
        }

        $advocateServiceTypes = $this->advocateServiceTypeRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($advocateServiceTypes as $advocateServiceType) {
            $response[] = $this->makeSingleTypeResponse($advocateServiceType);
        }
        return $response;
    }

    /**
     * @param AdvocateServiceType $advocateServiceType
     * @return array
     */
    public function makeSingleTypeResponse(AdvocateServiceType $advocateServiceType)
    {
        $singleData = [];
        $singleData['id'] = $advocateServiceType->getId();
        $singleData['name'] = $advocateServiceType->getName();
        $singleData['status'] = $advocateServiceType->getStatus();
        $singleData['created_on'] = $advocateServiceType->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
