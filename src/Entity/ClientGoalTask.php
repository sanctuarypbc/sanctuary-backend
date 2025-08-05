<?php

namespace App\Entity;

use App\Enum\TaskEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientGoalTaskRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class ClientGoalTask extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientGoal")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $clientGoal;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $completed = TaskEnum::STATUS_NOT_COMPLETED;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     * @return $this
     */
    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return ClientGoal|null
     */
    public function getClientGoal(): ?ClientGoal
    {
        return $this->clientGoal;
    }

    /**
     * @param ClientGoal|null $clientGoal
     * @return $this
     */
    public function setClientGoal(?ClientGoal $clientGoal): self
    {
        $this->clientGoal = $clientGoal;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     * @return $this
     */
    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTime|null $completedAt
     * @return $this
     */
    public function setCompletedAt(?\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
