<?php

namespace App\ApiBundle\Service;

use App\Entity\CovidQuestion;
use App\Entity\User;
use App\Enum\CovidQuestionEnum;
use App\Enum\UserEnum;
use App\Repository\CovidQuestionRepository;

/**
 * Class CovidQuestionService
 * @package App\ApiBundle\Service
 */
class CovidQuestionService
{
    /** @var CovidQuestionRepository */
    private $covidQuestionRepository;

    /**
     * CovidQuestionService constructor.
     * @param CovidQuestionRepository $covidQuestionRepository
     */
    public function __construct(CovidQuestionRepository $covidQuestionRepository)
    {
        $this->covidQuestionRepository = $covidQuestionRepository;
    }

    /**
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCovidQuestion(array $data)
    {
        $covidQuestion = new CovidQuestion();
        $covidQuestion->setText($data['text']);

        $this->covidQuestionRepository->persist($covidQuestion, true);
    }

    /**
     * @param int $covidQuestionId
     * @param array $data
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateCovidQuestion(int $covidQuestionId, array $data)
    {
        $covidQuestionObj = $this->covidQuestionRepository->findOneBy(['id' => $covidQuestionId, 'status' => CovidQuestionEnum::STATUS_ACTIVE]);
        if (!$covidQuestionObj instanceof CovidQuestion) {
            return "Covid question doesn't exist.";
        }

        $covidQuestionObj->setText($data['text']);
        $this->covidQuestionRepository->flush();

        return true;
    }

    /**
     * @param int $covidQuestionId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCovidQuestion(int $covidQuestionId)
    {
        $covidQuestionObj = $this->covidQuestionRepository->findOneBy(['id' => $covidQuestionId, 'status' => CovidQuestionEnum::STATUS_ACTIVE]);
        if (!$covidQuestionObj instanceof CovidQuestion) {
            return "Covid question doesn't exist.";
        }

        $covidQuestionObj->setStatus(CovidQuestionEnum::STATUS_DELETED);
        $this->covidQuestionRepository->flush();

        return true;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function getCovidQuestions(array $data)
    {
        return $this->covidQuestionRepository->getCovidQuestions($data);
    }
}
