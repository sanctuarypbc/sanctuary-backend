<?php

namespace App\ApiBundle\Service;

use App\Entity\FirstResponderDetail;
use App\Entity\FirstResponderType;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\FirstResponderDetailRepository;
use App\Repository\FirstResponderTypeRepository;

/**
 * Class FirstResponderTypeService
 * @package App\ApiBundle\Service
 */
class FirstResponderTypeService
{
    /** @var FirstResponderTypeRepository  */
    private $firstResponderTypeRepository;

    /** @var FirstResponderDetailRepository  */
    private $firstResponderDetailRepository;

    /**
     * FirstResponderTypeService constructor.
     * @param FirstResponderTypeRepository $firstResponderTypeRepository
     * @param FirstResponderDetailRepository $firstResponderDetailRepository
     */
    public function __construct(
        FirstResponderTypeRepository $firstResponderTypeRepository,
        FirstResponderDetailRepository $firstResponderDetailRepository
    ) {
        $this->firstResponderTypeRepository = $firstResponderTypeRepository;
        $this->firstResponderDetailRepository = $firstResponderDetailRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\FirstResponderType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addFirstResponderType($name)
    {
        return $this->firstResponderTypeRepository->addFirstResponderType($name);
    }

    /**
     * @param $firstResponderTypeId
     * @param $name
     */
    public function updateFirstResponderTypeById($firstResponderTypeId, $name)
    {
        return $this->firstResponderTypeRepository->updateFirstResponderTypeById($firstResponderTypeId, $name);
    }

    /**
     * @param $firstResponderTypeId
     * @return bool|void
     */
    public function deleteFirstResponderTypeById($firstResponderTypeId)
    {
        $linkedFirstResponder = $this->firstResponderDetailRepository
            ->findOneBy(['status' => StatusEnum::ACTIVE, 'firstResponderType' => $firstResponderTypeId]);
        if ($linkedFirstResponder instanceof FirstResponderDetail) {
            return false;
        }

        return $this->firstResponderTypeRepository->deleteFirstResponderTypeById($firstResponderTypeId);
    }

    /**
     * @param $id
     * @return FirstResponderType|null
     */
    public function getFirstResponderTypeById($id)
    {
        return $this->firstResponderTypeRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFirstResponderTypeLikeName($name)
    {
        return $this->firstResponderTypeRepository->getFirstResponderTypeLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return FirstResponderType|null
     */
    public function getFirstResponderTypeByIdAndName($id, $name)
    {
        return $this->firstResponderTypeRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $firstResponderTypeId
     * @param $name
     * @return array
     */
    public function getFirstResponderTypeList($firstResponderTypeId, $name)
    {
        $response = [];
        if ($firstResponderTypeId || $name) {
            $firstResponderType = empty($name) ? $this->getFirstResponderTypeById($firstResponderTypeId) : (
                empty($firstResponderTypeId) ? $this->getFirstResponderTypeLikeName($name) : $this->getFirstResponderTypeByIdAndName($firstResponderTypeId, $name)
            );
            if (!empty($firstResponderType) && is_array($firstResponderType)) {
                foreach ($firstResponderType as $item) {
                    $response[] = $this->makeSingleTypeResponse($item);
                }
            } elseif (!empty($firstResponderType)) {
                $response = $this->makeSingleTypeResponse($firstResponderType);
            }
            return $response;
        }

        $firstResponderTypes = $this->firstResponderTypeRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'Desc']);
        foreach ($firstResponderTypes as $firstResponderType) {
            $response[] = $this->makeSingleTypeResponse($firstResponderType);
        }
        return $response;
    }

    /**
     * @param FirstResponderType $firstResponderType
     * @return array
     */
    public function makeSingleTypeResponse(FirstResponderType $firstResponderType)
    {
        $singleData = [];
        $singleData['id'] = $firstResponderType->getId();
        $singleData['name'] = $firstResponderType->getName();
        $singleData['status'] = $firstResponderType->getStatus();
        $singleData['created_on'] = $firstResponderType->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
