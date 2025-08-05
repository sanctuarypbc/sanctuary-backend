<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsfeedFileRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class NewsfeedFile extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Newsfeed")
     * @ORM\JoinColumn(nullable=false)
     */
    private $newsFeed;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Newsfeed|null
     */
    public function getNewsFeed(): ?NewsFeed
    {
        return $this->newsFeed;
    }

    /**
     * @param NewsFeed|null $newsFeed
     * @return $this
     */
    public function setNewsFeed(?NewsFeed $newsFeed): self
    {
        $this->newsFeed = $newsFeed;

        return $this;
    }
}
