<?php

namespace App\Repository;

use App\Entity\Sms;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sms[]    findAll()
 * @method Sms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsRepository extends AbstractRepository
{
    /**
     * SmsRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sms::class);
    }

    public function removeSms($smsObj){
        $this->getEntityManager()->remove($smsObj);
        $this->getEntityManager()->flush();
    }
}
