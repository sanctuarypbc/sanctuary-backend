<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\CovidAnswer;
use App\Entity\CovidQuestion;
use App\Entity\User;
use App\Enum\CovidAnswerEnum;
use App\Enum\UserEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\CovidAnswerRepository;
use App\Repository\CovidQuestionRepository;


/**
 * Class CovidAnswerService
 * @package App\ApiBundle\Service
 */
class CovidAnswerService
{
    /** @var CovidAnswerRepository */
    private $covidAnswerRepository;

    /**
     * @var $covidQuestionRepository
     */
    private $covidQuestionRepository;

    /**
     * @var $clientDetailRepository
     */
    private $clientDetailRepository;

    /**
     * CovidAnswerService constructor.
     * @param CovidAnswerRepository $covidAnswerRepository
     *
     */
    public function __construct(
        CovidAnswerRepository $covidAnswerRepository,
        CovidQuestionRepository $covidQuestionRepository,
        ClientDetailRepository $clientDetailRepository
    )
    {
        $this->covidAnswerRepository = $covidAnswerRepository;
        $this->covidQuestionRepository = $covidQuestionRepository;
        $this->clientDetailRepository = $clientDetailRepository;
    }

    /**
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCovidAnswer(array $data)
    {
        $clientDetail = $this->clientDetailRepository->findOneBy(['id' =>$data['client_detail_id']]);
        if(! $clientDetail instanceof ClientDetail){
            return "Client Id is invalid";
        }
        $covidQuestion = $this->covidQuestionRepository->findOneBy(['id'=>$data['covid_question_id']]);

        if(! $covidQuestion instanceof CovidQuestion){
            return "Question id is invalid";
        }
        $covidAnswer = new CovidAnswer();
        $covidAnswer->setValue(json_encode($data['value']));
        $covidAnswer->setClientDetail($clientDetail);
        $covidAnswer->setCovidQuestion($covidQuestion);

        $this->covidAnswerRepository->persist($covidAnswer, true);
        return true;
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateCovidAnswer(int $id, array $data)
    {
        $covidAnswerObj = $this->covidAnswerRepository->findOneBy(['id' => $id, 'status' => CovidAnswerEnum::STATUS_ACTIVE]);
        if (!$covidAnswerObj instanceof CovidAnswer) {
            return "Covid Answer doesn't exist.";
        }

        $covidAnswerObj->setValue(json_encode($data['value']));
        $this->covidAnswerRepository->flush();

        return true;
    }

    /**
     * @param int $id
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCovidAnswer(int $id)
    {
        $covidAnswerObj = $this->covidAnswerRepository->findOneBy(['id' => $id, 'status' => CovidAnswerEnum::STATUS_ACTIVE]);
        if (!$covidAnswerObj instanceof CovidAnswer) {
            return "Covid Answer doesn't exist.";
        }

        $covidAnswerObj->setStatus(CovidAnswerEnum::STATUS_DELETED);
        $this->covidAnswerRepository->flush();

        return true;
    }

    /**
     * @param int $clientDetailId
     * @return mixed
     */
    public function getCovidAnswer(int $clientDetailId)
    {
        $answers = $this->covidAnswerRepository->findBy(['clientDetail' => $clientDetailId, 'status' => CovidAnswerEnum::STATUS_ACTIVE]);
        $response = [];
        foreach ($answers as $ans) {
            $temp = [];
            $temp[$ans->getCovidQuestion()->getId()]['values'] = json_decode($ans->getValue());
            $temp[$ans->getCovidQuestion()->getId()]['answer_id'] = $ans->getId();
            array_push($response, $temp);
        }

        return $response;
    }
}
