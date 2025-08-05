<?php

namespace App\Repository;

use App\Entity\CovidQuestion;
use App\Enum\CovidQuestionEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CovidQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CovidQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CovidQuestion[]    findAll()
 * @method CovidQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CovidQuestionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovidQuestion::class);
    }

    /**
     * @param array $data
     * @return int|mixed|string
     */
    public function getCovidQuestions(array $data)
    {
        $qb = $this->createQueryBuilder('cq')
            ->select('cq.id, cq.text, cq.created, cq.updated')
            ->andWhere('cq.status = :status')
            ->setParameter('status', CovidQuestionEnum::STATUS_ACTIVE)
            ->orderBy('cq.sequence', 'ASC');

        if (!empty($data['id'])) {
            $qb->andWhere('cq.id = :covidQuestionId')
                ->setParameter('covidQuestionId', $data['id']);
        }

        return $qb->getQuery()->getResult();
    }
}
