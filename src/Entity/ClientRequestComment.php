<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRequestCommentRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientRequestComment extends AbstractEntity
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientRequest")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientRequest;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return $this
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

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
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ClientRequest|null
     */
    public function getClientRequest(): ?ClientRequest
    {
        return $this->clientRequest;
    }

    /**
     * @param ClientRequest|null $clientRequest
     * @return $this
     */
    public function setClientRequest(?ClientRequest $clientRequest): self
    {
        $this->clientRequest = $clientRequest;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
        ];
    }
}
