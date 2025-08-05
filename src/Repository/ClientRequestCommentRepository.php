<?php

namespace App\Repository;

use App\Entity\ClientRequestComment;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\RequestEnum;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method ClientRequestComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientRequestComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientRequestComment[]    findAll()
 * @method ClientRequestComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRequestCommentRepository extends AbstractRepository
{
    /**
     * ClientRequestCommentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientRequestComment::class);
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount(User $user, array $data)
    {
        $qb = $this->createQueryBuilder('crc')
            ->select('COUNT(1) as count');

        $qb = $this->addFilters($qb, $user, $data);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getClientRequestComments(User $user, array $data)
    {
        $perPage = empty($data['per_page']) ? CommonEnum::PER_PAGE_DEFAULT : (int) $data['per_page'];
        $page = empty($data['page']) ? 1 : (int) $data['page'];
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('crc')
            ->select('crc.id, crc.text, crc.created, crc.updated, u.firstName as commentator')
            ->addSelect('CASE WHEN crc.user = :user THEN 1 ELSE 0 END AS own_comment')
            ->setParameter('user', $user)
            ->orderBy('crc.created' , 'DESC')
            ->setFirstResult(($page-1) * $perPage)
            ->setMaxResults($perPage);

        $qb = $this->addFilters($qb, $user, $data);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param User $user
     * @param array $data
     * @return QueryBuilder
     */
    public function addFilters(QueryBuilder $qb, User $user, array $data)
    {
        $qb->innerJoin('crc.user', 'u')
            ->where('crc.status = :status')
            ->andWhere('crc.clientRequest = :clientRequest')
            ->setParameter('status', RequestEnum::STATUS_ACTIVE)
            ->setParameter('clientRequest', $data['client_request_id']);

        if (!empty($data['id'])) {
            $qb->andWhere('crc.id = :id')
                ->setParameter('id', $data['id']);
        }

        return $qb;
    }
}
