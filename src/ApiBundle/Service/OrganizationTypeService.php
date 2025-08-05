<?php

namespace App\ApiBundle\Service;

use App\Entity\OrganizationType;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\OrganizationTypeRepository;

/**
 * Class OrganizationTypeService
 * @package App\ApiBundle\Service
 */
class OrganizationTypeService
{
    /** @var OrganizationTypeRepository  */
    private $organizationTypeRepository;

    /**
     * OrganizationService constructor.
     * @param OrganizationTypeRepository $organizationTypeRepository
     */
    public function __construct(OrganizationTypeRepository $organizationTypeRepository)
    {
        $this->organizationTypeRepository = $organizationTypeRepository;
    }

    /**
     * @param $id
     * @return OrganizationType|null
     */
    public function getOrganizationTypeById($id)
    {
        return $this->organizationTypeRepository->findOneBy(['status' => StatusEnum::ACTIVE, 'id' => $id]);
    }

    /**
     * @param OrganizationType $organizationType
     * @return array
     */
    public function makeSingleOrganizationTypeResponse(OrganizationType $organizationType)
    {
        $singleData = [];
        $singleData['id'] = $organizationType->getId();
        $singleData['name'] = $organizationType->getName();
        $singleData['status'] = $organizationType->getStatus();
        $singleData['created_on'] = $organizationType->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
