<?php

namespace App\Repository;

use App\Entity\FirstResponderDetail;
use App\Entity\FirstResponderType;
use App\Entity\Organization;
use App\Entity\User;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FirstResponderDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method FirstResponderDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method FirstResponderDetail[]    findAll()
 * @method FirstResponderDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FirstResponderDetailRepository extends AbstractRepository
{
    /**
     * FirstResponderDetailRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FirstResponderDetail::class);
    }

    /**
     * @param FirstResponderDetail $firstResponderDetail
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFirstResponderDetail(FirstResponderDetail $firstResponderDetail)
    {
        $firstResponderDetail->setStatus(StatusEnum::INACTIVE);
        $firstResponderDetail->getUser()->setEnabled(StatusEnum::INACTIVE);

        $this->flush();
    }

    /**
     * @param User $user
     * @param array $data
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFRDetailByData(User $user, array $data)
    {
        $firstResponderObj = new FirstResponderDetail();
        $firstResponderObj->setUser($user);
        $firstResponderObj->setNickName(isset($data['nick_name']) ? $data['nick_name'] : null);
        $firstResponderObj->setOfficePhone(isset($data['office_phone']) ? $data['office_phone'] : null);
        $firstResponderObj->setIdentificationNumber(isset($data['identification_number']) ? $data['identification_number'] : null);

        if (isset($data['type_id'])) {
            $frTypeObj = $this->getEntityManager()->getRepository(FirstResponderType::class)
                ->findOneBy(['id' => $data['type_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($frTypeObj)) {
                return ["status" => false, "message" => "Invalid First Responder Type provided."];
            }
            $firstResponderObj->setFirstResponderType($frTypeObj);
        }

        if (isset($data['organization_id'])) {
            $organizationObj = $this->getEntityManager()->getRepository(Organization::class)
                ->findOneBy(['id' => $data['organization_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($organizationObj)) {
                return ["status" => false, "message" => "Invalid Organization provided."];
            }
            $firstResponderObj->setOrganization($organizationObj);
        }

        $this->persist($firstResponderObj);
        return ["status" => true];
    }
}
