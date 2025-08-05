<?php

namespace App\Entity;

use ActivityLogBundle\Entity\Interfaces\StringableInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package App\Entity
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
abstract class AbstractEntity implements StringableInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", options={"default"=true})
     * @Gedmo\Versioned
     */
    protected $status = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    /**
     * @return string
     */
    public function toString()
    {
        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }
        return get_class($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return AbstractEntity
     */
    public function setCreated(\DateTime $created): AbstractEntity
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return AbstractEntity
     */
    public function setUpdated(\DateTime $updated): AbstractEntity
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return AbstractEntity
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
