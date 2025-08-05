<?php

namespace App\Repository;

use App\Entity\ResetToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResetToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetToken[]    findAll()
 * @method ResetToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetTokenRepository extends AbstractRepository
{
    /**
     * ResetTokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetToken::class);
    }

    /**
     * @param User $user
     * @param \DateTime $expiry
     * @param string $token
     * @return ResetToken
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addUserRestToken(User $user, \DateTime $expiry, string $token)
    {
        $resetToken = new ResetToken();
        $resetToken->setUser($user)
            ->setExpiry($expiry)
            ->setToken($token);

        $this->persist($resetToken, true);
        return $resetToken;
    }
}
