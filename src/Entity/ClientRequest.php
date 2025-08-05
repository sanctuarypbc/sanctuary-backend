<?php

namespace App\Entity;

use App\Enum\RequestEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRequestRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientRequest extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientDetail")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $clientDetail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Request")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $request;

    /**
     * @ORM\Column(type="smallint")
     * @Gedmo\Versioned
     */
    protected $status = RequestEnum::CLIENT_REQUEST_PENDING;

    /**
     * @return ClientDetail|null
     */
    public function getClientDetail(): ?ClientDetail
    {
        return $this->clientDetail;
    }

    /**
     * @param ClientDetail|null $clientDetail
     * @return $this
     */
    public function setClientDetail(?ClientDetail $clientDetail): self
    {
        $this->clientDetail = $clientDetail;

        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     * @return $this
     */
    public function setRequest(?Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return ClientRequest
     */
    public function setStatus($status): ClientRequest
    {
        $this->status = $status;
        return $this;
    }
}
