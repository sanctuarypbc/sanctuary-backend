<?php

namespace App\Repository;

use App\Entity\Newsfeed;
use App\Enum\NewsfeedEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Newsfeed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newsfeed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newsfeed[]    findAll()
 * @method Newsfeed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsfeedRepository extends AbstractRepository
{
    /**
     * NewsfeedRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsfeed::class);
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountByShowToClient()
    {
        return $this->createQueryBuilder('n')
            ->select('count(n.id)')
            ->where('n.showToClient = :showToClient')
            ->setParameter('showToClient', NewsfeedEnum::SHOW_TO_CLIENT)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $data
     * @param bool $showToClientNewsfeedsOnly
     * @return int|mixed|string
     */
    public function getNewsfeeds(array $data, bool $showToClientNewsfeedsOnly)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n.id, n.headline, n.description, n.showToClient as show_to_client, n.created, n.updated')
            ->where('n.status = :status')
            ->setParameter('status', NewsfeedEnum::STATUS_ACTIVE)
            ->orderBy('n.created', 'DESC');

        if (!empty($data['id'])) {
            $qb->andWhere('n.id = :id')
                ->setParameter('id', (int)$data['id']);
        }

        if ($showToClientNewsfeedsOnly === true) {
            $qb->andWhere('n.showToClient = :showToClient')
                ->setParameter('showToClient', NewsfeedEnum::SHOW_TO_CLIENT)
                ->setMaxResults(NewsfeedEnum::MAX_SHOW_TO_CLIENT);
        }

        return $qb->getQuery()->getResult();
    }
}
