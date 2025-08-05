<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientGoalRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientGoal extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientDetail")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientDetail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Goal")
     * @ORM\JoinColumn(nullable=false)
     */
    private $goal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assignedBy;

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
     * @return Goal|null
     */
    public function getGoal(): ?Goal
    {
        return $this->goal;
    }

    /**
     * @param Goal|null $goal
     * @return $this
     */
    public function setGoal(?Goal $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignedBy(): ?User
    {
        return $this->assignedBy;
    }

    /**
     * @param User|null $assignedBy
     * @return $this
     */
    public function setAssignedBy(?User $assignedBy): self
    {
        $this->assignedBy = $assignedBy;

        return $this;
    }
}
