<?php

namespace App\ApiBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twilio\Rest\Client;

/**
 * Class TwilioService
 * @package App\ApiBundle\Service
 */
class TwilioService
{
    const MOBILE_CARRIER_TYPE = 'mobile';
    const LANDLINE_CARRIER_TYPE = 'landline';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $twilioFrom;

    /**
     * @var LoggerInterface
     */
    private $securityLogger;

    /**
     * TwilioService constructor.
     * @param ContainerBagInterface $params
     * @param LoggerInterface $securityLogger
     */
    public function __construct(ContainerBagInterface $params, LoggerInterface $securityLogger)
    {
        $this->id = $params->get('twilio-id');
        $this->secret = $params->get('twilio-secret');
        $this->twilioFrom = $params->get('twilio-from');
        $this->securityLogger = $securityLogger;
    }

    /**
     * @param $text
     * @param $to
     * @return bool
     */
    public function sendMessage($text, $to)
    {
        try {
            $twilio = $this->getTwilioClient();

            $message = $twilio->messages
                ->create($to, ["body" => $text, "from" => $this->twilioFrom]);

            if (empty($message->sid)) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            $this->securityLogger->error('[twilio_message_error] : ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param $number
     * @return bool|string
     */
    public function isValidNumber($number)
    {
        if (empty($number)) {
            return false;
        }

        try {
            $twilio = $this->getTwilioClient();

            $response = $twilio->lookups->v1->phoneNumbers($number)
                ->fetch(["type" => ["carrier"]]);
            $response = $response->toArray();

            if (!empty($response['carrier']) && !empty($response['carrier']['type']) &&
                $response['carrier']['type'] === self::MOBILE_CARRIER_TYPE) {
                return empty($response['phoneNumber']) ? $number : $response['phoneNumber'];
            }

            return false;
        } catch (\Exception $exception) {
            $this->securityLogger->error('[twilio_lookup_error] : ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * @return Client
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    private function getTwilioClient()
    {
        return new Client($this->id, $this->secret);
    }
}
