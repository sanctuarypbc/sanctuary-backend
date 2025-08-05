<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResetTokenRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ResetToken extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiry;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="resetTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return ResetToken
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiry(): ?\DateTimeInterface
    {
        return $this->expiry;
    }

    /**
     * @param \DateTimeInterface $expiry
     * @return ResetToken
     */
    public function setExpiry(\DateTimeInterface $expiry): self
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return ResetToken
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
