<?php

namespace App\ApiBundle\Service;

use App\Repository\ResetTokenRepository;
use FOS\UserBundle\Model\UserManager;

/**
 * Class ResetTokenService
 * @package App\ApiBundle\Service
 */
class ResetTokenService
{
    /** @var ResetTokenRepository  */
    private $resetTokenRepository;

    /**
     * UserService constructor.
     * @param ResetTokenRepository $resetTokenRepository
     */
    public function __construct(ResetTokenRepository $resetTokenRepository)
    {
        $this->resetTokenRepository = $resetTokenRepository;
    }

    /**
     * @param string $token
     * @return \App\Entity\ResetToken|null
     */
    public function getResetTokenByToken(string $token)
    {
        return $this->resetTokenRepository->findOneBy(['token' => $token]);
    }
}
