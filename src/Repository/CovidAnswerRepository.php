<?php

namespace App\Repository;

use App\Entity\CovidAnswer;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CovidAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method CovidAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method CovidAnswer[]    findAll()
 * @method CovidAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CovidAnswerRepository extends AbstractRepository
{
    /**
     * CovidAnswerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovidAnswer::class);
    }
}
