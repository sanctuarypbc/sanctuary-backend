<?php

namespace App\Repository;

use App\Entity\WellnessTip;
use App\Enum\WellnessTipEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method WellnessTip|null find($id, $lockMode = null, $lockVersion = null)
 * @method WellnessTip|null findOneBy(array $criteria, array $orderBy = null)
 * @method WellnessTip[]    findAll()
 * @method WellnessTip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WellnessTipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WellnessTip::class);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getWellnessTip($data)
    {
        $sql = $this->createQueryBuilder('w')
            ->select('w.id, w.heading, w.body, w.icon, w.image, w.media')
            ->orderBy('w.created', 'DESC')
            ->andWhere('w.status =:status')
            ->setParameter('status', WellnessTipEnum::STATUS_ACTIVE);

        if (!empty($data['id'])) {
            $sql->andWhere('w.id =:id')
                ->setParameter('id',$data['id']);
        } elseif (!empty($data['range'])) {
            $sql->setMaxResults($data['range']);
        } else { }

        return $sql->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
