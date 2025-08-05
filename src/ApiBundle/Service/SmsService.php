<?php

namespace App\ApiBundle\Service;

use App\Entity\Sms;
use App\Enum\CommonEnum;
use App\Repository\SmsRepository;

/**
 * Class SmsService
 * @package App\ApiBundle\Service
 */
class SmsService
{
    /** @var SmsRepository  */
    private $smsRepository;

    /** @var TwilioService  */
    private $twilioService;

    /**
     * SmsService constructor.
     * @param SmsRepository $smsRepository
     * @param TwilioService $twilioService
     */
    public function __construct(
        SmsRepository $smsRepository,
        TwilioService $twilioService
    ) {
        $this->smsRepository = $smsRepository;
        $this->twilioService = $twilioService;
    }

    /**
     * @param int $phoneNumber
     * @return Sms|null
     */
    public function getSmsByPhone($phoneNumber)
    {
        return $this->smsRepository->findOneBy(['phoneNumber' => $phoneNumber]);
    }

    /**
     * @param int $id
     * @return Sms|null
     */
    public function getSmsById($id)
    {
        return $this->smsRepository->findOneBy(['id' => $id, 'status' => 1]);
    }

    /**
     * @return Sms[]
     */
    public function getAllActivePhoneNumber()
    {
        return $this->smsRepository->findBy(['status' => 1], ['id' => 'Desc']);
    }

    /**
     * @param $sms
     * @return array
     */
    public function makeSmsApiResponse($sms)
    {
        $response = [];
        foreach ($sms as $contact) {
            $response[] = $this->makeSingleSmsResponse($contact);
        }
        return $response;
    }

    /**
     * @param $contact
     * @return array|bool
     */
    public function makeSingleSmsResponse($contact)
    {
        if (!$contact instanceof Sms) {
            return false;
        }
        $singleData = $contact->toArray();
        $singleData['created_on'] = $contact->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }

    /**
     * @param $data
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createSmsByRequestData($data)
    {
//        if (! ($phone = $this->twilioService->isValidNumber($data['phone_number']))) {
//            return false;
//        }

        $sms = new Sms();
        $sms->setStatus(1);
        $sms->setPhoneNumber($data['phone_number']);
        $this->smsRepository->persist($sms, true);

        return true;
    }

    /**
     * @param int $smsId
     * @param $data
     * @return bool|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateSmsInfo(int $smsId, $data)
    {
//        if (! ($phone = $this->twilioService->isValidNumber($data['phone_number']))) {
//            return "Invalid phone number provided.";
//        }

        $smsObj = $this->smsRepository->findOneBy(['id' => $smsId]);
        if (!$smsObj instanceof Sms) {
            return "Sms contact doesn't exist.";
        }

        $smsObj->setPhoneNumber($data['phone_number']);

        $this->smsRepository->flush();
        return true;
    }

    /**
     * @param int $smsId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSms(int $smsId)
    {
        $smsObj = $this->smsRepository->findOneBy(['id' => $smsId, 'status' => 1]);
        if (!$smsObj instanceof Sms) {
            return "Sms contact doesn't exist.";
        }

        $this->smsRepository->removeSms($smsObj);

        return true;
    }
}
