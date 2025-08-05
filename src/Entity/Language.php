<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Language extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\AdvocateDetail", mappedBy="language")
     */
    private $advocateDetails;

    /**
     * Language constructor.
     */
    public function __construct()
    {
        $this->advocateDetails = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Language
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     * @return Language
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection|AdvocateDetail[]
     */
    public function getAdvocateDetails(): Collection
    {
        return $this->advocateDetails;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return Language
     */
    public function addAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if (!$this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails[] = $advocateDetail;
            $advocateDetail->addLanguage($this);
        }

        return $this;
    }

    /**
     * @param AdvocateDetail $advocateDetail
     * @return Language
     */
    public function removeAdvocateDetail(AdvocateDetail $advocateDetail): self
    {
        if ($this->advocateDetails->contains($advocateDetail)) {
            $this->advocateDetails->removeElement($advocateDetail);
            $advocateDetail->removeLanguage($this);
        }

        return $this;
    }
}
