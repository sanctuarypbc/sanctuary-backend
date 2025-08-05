<?php

namespace App\ApiBundle\Service;

use App\Enum\CommonEnum;
use Doctrine\ORM\EntityManagerInterface;
use FOS\OAuthServerBundle\Entity\AccessTokenManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

/**
 * Class LoginService
 * @package App\ApiBundle\Service
 */
class LoginService
{
    /** @var UserService  */
    private $userService;

    /** @var TwilioService  */
    private $twilioService;

    /** @var ClientService  */
    private $clientService;

    /** @var mixed  */
    private $specialClientId;

    /** @var mixed  */
    private $specialSmsHash;

    /** @var Security  */
    private $security;

    /** @var AccessTokenManager  */
    private $accessTokenManager;

    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * LoginService constructor.
     * @param UserService $userService
     * @param TwilioService $twilioService
     * @param ClientService $clientService
     * @param ContainerBagInterface $params
     * @param Security $security
     * @param AccessTokenManager $accessTokenManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserService $userService,
        TwilioService $twilioService,
        ClientService $clientService,
        ContainerBagInterface $params,
        Security $security,
        AccessTokenManager $accessTokenManager,
        EntityManagerInterface $entityManager
    ) {
        $this->userService = $userService;
        $this->twilioService = $twilioService;
        $this->clientService = $clientService;
        $this->specialClientId = $params->get('special-client-id');
        $this->specialSmsHash = $params->get('special-sms-hash');
        $this->security = $security;
        $this->accessTokenManager = $accessTokenManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $phone
     * @return array|false
     */
    public function loginUser($phone)
    {
        if (!($phone = $this->twilioService->isValidNumber($phone))) {
            return false;
        }

        $user = $this->userService->findUserByPhone($phone);
        if (!$user instanceof User) {
            $user = $this->userService->createUser($phone);
            $this->entityManager->getRepository('App:ClientDetail')->addClientDetailsByData($user, []);
        }

        $code = $this->userService->getVerificationCode();
        $user->setVerificationCode($code);
        $user->setVerificationRequestedAt(new \DateTime());
        $this->userService->updateUser($user);
        $this->twilioService->sendMessage($this->getSms($code), $phone);
        return ['code' => $code];
    }

    /**
     * @param $code
     * @return string
     */
    public function getSms($code)
    {
        $text = str_replace('{code}', $code, CommonEnum::MESSAGE_TEXT);

        if ($this->getCurrentClientId() === $this->specialClientId) {
            $text .= "\n\n" . $this->specialSmsHash;
        }

        return $text;
    }

    /**
     * @param $phone
     * @param $code
     * @return array|bool|string
     * @throws \Exception
     */
    public function verifyCode($phone, $code)
    {
        if (! ($phone = $this->twilioService->isValidNumber($phone))) {
            return "Invalid phone number provided.";
        }

        $user = $this->userService->findUserByPhone($phone);

        if (!$user instanceof User || $user->getVerificationCode() !== $code) {
            return "Invalid code provided.";
        }

        $user->setVerificationCode(null);
        $user->setVerificationRequestedAt(null);
        $this->userService->updateUser($user);
        $returnArray = $this->userService->getAccessToken($user);
        $returnArray['id'] = $user->getId();

        return $returnArray;
    }

    /**
     * @return mixed
     */
    public function getCurrentClientId()
    {
        $accessToken = $this->accessTokenManager->findTokenByToken(
            $this->security->getToken()->getToken()
        );

        return $accessToken->getClient()->getRandomId();
    }
}
