<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CovidAnswerRepository")
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class CovidAnswer extends AbstractEntity
{
    /**
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClientDetail")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientDetail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CovidQuestion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $covidQuestion;

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return ClientDetail|null
     */
    public function getClientDetail(): ClientDetail
    {
        return $this->clientDetail;
    }

    /**
     * @param ClientDetail|null $clientDetail
     * @return $this
     */
    public function setClientDetail(ClientDetail $clientDetail): self
    {
        $this->clientDetail = $clientDetail;

        return $this;
    }

    /**
     * @return CovidQuestion|null
     */
    public function getCovidQuestion(): CovidQuestion
    {
        return $this->covidQuestion;
    }

    /**
     * @param CovidQuestion|null $covidQuestion
     * @return $this
     */
    public function setCovidQuestion(CovidQuestion $covidQuestion): self
    {
        $this->covidQuestion = $covidQuestion;

        return $this;
    }
}
