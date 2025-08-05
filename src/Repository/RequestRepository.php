<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\RequestEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Request|null find($id, $lockMode = null, $lockVersion = null)
 * @method Request|null findOneBy(array $criteria, array $orderBy = null)
 * @method Request[]    findAll()
 * @method Request[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestRepository extends AbstractRepository
{
    /**
     * RequestRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getRequests(User $user, array $data)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.id, r.title, r.description, r.isDefault, r.created, r.updated')
            ->where('r.isDefault = :defaultStatus OR r.user = :user')
            ->andWhere('r.status = :status')
            ->setParameter('defaultStatus', RequestEnum::STATUS_DEFAULT)
            ->setParameter('user', $user)
            ->setParameter('status', RequestEnum::STATUS_ACTIVE)
            ->orderBy('r.isDefault', 'DESC');

        if (!empty($data['id'])) {
            $qb->andWhere('r.id = :requestId')
                ->setParameter('requestId', $data['id']);
        }

        return $qb->getQuery()->getResult();
    }
}
