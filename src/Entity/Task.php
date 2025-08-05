<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Task extends AbstractEntity
{
    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Goal")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $goal;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

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
}
