<?php

namespace App\Repository;

use App\Entity\AdvocateDetail;
use App\Entity\AdvocateServiceType;
use App\Entity\Language;
use App\Entity\Organization;
use App\Entity\User;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdvocateDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvocateDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvocateDetail[]    findAll()
 * @method AdvocateDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvocateDetailRepository extends AbstractRepository
{
    /**
     * AdvocateDetailRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvocateDetail::class);
    }

    /**
     * @param array $filterParameters
     * @return mixed
     */
    public function getFilteredAdvocates(array $filterParameters)
    {
        $qb = $this->createQueryBuilder('ad')
            ->leftJoin('ad.user', 'user')
            ->where('ad.status = :adStatus')
            ->setParameter('adStatus', StatusEnum::ACTIVE)
            ->orderBy('user.id',  'Desc');

        if (!empty($filterParameters['id'])) {
            $qb->andWhere('user.id = :userId')
                ->setParameter('userId', $filterParameters['id']);
        }

        if (!empty(trim($filterParameters['searchText']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('user.firstName', ':searchText'),
                $qb->expr()->like('user.lastName', ':searchText')
            ));
            $qb->setParameter('searchText', '%' . $filterParameters['searchText'] . '%');
        }

        if (!empty($filterParameters['identifier'])) {
            $qb->andWhere('ad.identifier = :identifier')
                ->setParameter('identifier', $filterParameters['identifier']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAdvocateDetail(AdvocateDetail $advocateDetail)
    {
        $advocateDetail->setStatus(StatusEnum::INACTIVE);
        $advocateDetail->getUser()->setEnabled(StatusEnum::INACTIVE);

        $this->flush();
    }

    /**
     * @param User $user
     * @param $data
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAdvocateDetailByData(User $user, $data)
    {
        $advocateDetailObj = new AdvocateDetail();
        $advocateDetailObj->setUser($user);
        $advocateDetailObj->setIdentifier(isset($data['identifier']) ? $data['identifier'] : null);
        $advocateDetailObj->setAdditionalPhone(isset($data['additional_phone']) ? $data['additional_phone'] : null);
        $advocateDetailObj->setEmergencyContact(isset($data['emergency_contact']) ? $data['emergency_contact'] : null);

        if (isset($data['service_type_id'])) {
            $advocateServiceTypeObj = $this->getEntityManager()->getRepository(AdvocateServiceType::class)
                ->findOneBy(['id' => $data['service_type_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($advocateServiceTypeObj)) {
                return ["status" => false, "message" => "Invalid Service Type provided."];
            }
            $advocateDetailObj->setServiceType($advocateServiceTypeObj);
        }

        if (isset($data['organization_id'])) {
            $organizationObj = $this->getEntityManager()->getRepository(Organization::class)
                ->findOneBy(['id' => $data['organization_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($organizationObj)) {
                return ["status" => false, "message" => "Invalid Organization provided."];
            }
            $advocateDetailObj->setOrganization($organizationObj);
        }

        if (isset($data['language_ids'])){
            foreach (explode(',', $data['language_ids']) as $languageId) {
                $languageObj = $this->getEntityManager()->getRepository(Language::class)
                    ->findOneBy(['id' => $languageId, 'status' => StatusEnum::ACTIVE]);
                if (empty($languageObj)) {
                    return ["status" => false, "message" => "Invalid Language provided."];
                }
                $advocateDetailObj->addLanguage($languageObj);
            }
        }

        $this->persist($advocateDetailObj);
        return ["status" => true];
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeAdvocateLanguages(AdvocateDetail $advocateDetail)
    {
        foreach ($advocateDetail->getLanguage() as $language) {
            $advocateDetail->removeLanguage($language);
        }
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAdvocateClientsCount(AdvocateDetail $advocateDetail)
    {
        return $this->createQueryBuilder('ad')
            ->select('count(cd.id)')
            ->leftJoin('ad.clientDetails', 'cd')
            ->where('cd.status = :cdStatus')
            ->andWhere('ad.id = :advocateDetail')
            ->setParameter('cdStatus', StatusEnum::ACTIVE)
            ->setParameter('advocateDetail', $advocateDetail)
            ->getQuery()->getSingleScalarResult();
    }
}
