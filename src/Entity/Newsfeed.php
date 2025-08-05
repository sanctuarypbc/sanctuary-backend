<?php

namespace App\Entity;

use App\Enum\NewsfeedEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsfeedRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Newsfeed extends AbstractEntity
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $headline;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showToClient = NewsfeedEnum::DONT_SHOT_TO_CLIENT;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;

    /**
     * @return string|null
     */
    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    /**
     * @param string|null $headline
     * @return $this
     */
    public function setHeadline(?string $headline): self
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getShowToClient(): ?bool
    {
        return $this->showToClient;
    }

    /**
     * @param bool $showToClient
     * @return $this
     */
    public function setShowToClient(bool $showToClient): self
    {
        $this->showToClient = $showToClient;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return $this
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
