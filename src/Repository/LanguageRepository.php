<?php

namespace App\Repository;

use App\Entity\Language;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends AbstractRepository
{
    /**
     * LanguageRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getActiveLanguagesData($id)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.name, l.locale, l.id')
            ->andWhere('l.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE);

        if ($id) {
            $qb->andWhere('l.id = :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }
}
