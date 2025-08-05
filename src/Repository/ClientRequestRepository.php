<?php

namespace App\Repository;

use App\Entity\ClientDetail;
use App\Entity\ClientRequest;
use App\Entity\User;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientRequest[]    findAll()
 * @method ClientRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRequestRepository extends AbstractRepository
{
    /**
     * ClientRequestRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientRequest::class);
    }

    /**
     * @param User $user
     * @param array $data
     * @param ClientDetail|null $clientDetail
     * @return int|mixed|string
     */
    public function getClientRequests(User $user, array $data, ClientDetail $clientDetail = null)
    {
        $qb = $this->createQueryBuilder('cr')
            ->select('cr.id, r.id as request_id, cr.status, r.title, r.isDefault, r.description, cr.created, cr.updated')
            ->innerJoin('cr.request', 'r')
            ->where('cr.clientDetail = :clientDetail')
            ->andwhere('cr.status >= :status')
            ->orderBy('cr.created', 'DESC')
            ->setParameter('status', StatusEnum::ACTIVE);


        if (!empty($data['id'])) {
            $qb->andWhere('cr.id = :clientRequestId')
                ->setParameter('clientRequestId', $data['id']);
        }

        if (in_array(UserEnum::ROLE_ADVOCATE, $user->getRoles())) {
            $qb->setParameter('clientDetail', $clientDetail);
        } else {
            $qb->setParameter('clientDetail', $user->getClientDetail());
        }

        return $qb->getQuery()->getResult();
    }
}
