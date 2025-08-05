<?php

namespace App\ApiBundle\Service;

use App\ApiBundle\Entity\Client;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use FOS\OAuthServerBundle\Entity\AccessTokenManager;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 * @package App\ApiBundle\Service
 */
class UserService
{
    /** @var UserManager $userManager */
    private $userManager;

    /** @var UserRepository  */
    private $userRepository;

    /** @var AccessTokenManager  */
    private $accessTokenManager;

    /** @var ClientRepository  */
    private $clientRepository;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    /**
     * UserService constructor.
     * @param UserManager $userManager
     * @param UserRepository $userRepository
     * @param AccessTokenManager $accessTokenManager
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        UserManager $userManager,
        UserRepository $userRepository,
        AccessTokenManager $accessTokenManager,
        ClientRepository $clientRepository,
        UserPasswordEncoderInterface $encoder,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->accessTokenManager = $accessTokenManager;
        $this->clientRepository = $clientRepository;
        $this->encoder = $encoder;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string $email
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    public function getUserByEmail(string $email)
    {
        return $this->userManager->findUserByEmail($email);
    }

    /**
     * @param string $phoneNumber
     * @return User|null
     */
    public function checkUserByPhoneNumber(string $phoneNumber)
    {
        return $this->userRepository->findOneBy(['phone' => $phoneNumber]);
    }

    /**
     * @param string $username
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    public function getUserByUsername(string $username)
    {
        return $this->userManager->findUserByUsername($username);
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function updateUserPassword(User $user, string $password)
    {
        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserDetailResponseByUser(User $user)
    {
        $userResponse = [];
        $userResponse['id'] = $user->getId();
        $userResponse['first_name'] = $user->getFirstName();
        $userResponse['last_name'] = $user->getLastName();
        $userResponse['username'] = $user->getUsername();
        $userResponse['email'] = $user->getEmail();
        $userResponse['phone'] = $user->getPhone();
        $userResponse['dob'] = $user->getDob();
        $userResponse['gender'] = $user->getGender();
        $userResponse['roles'] = $user->getRoles();

        if (in_array(UserEnum::ROLE_FACILITY, $user->getRoles())) {
            $userResponse['facility_id'] =$user->getFacility()->getId();
            $userResponse['primary_color'] =$user->getFacility()->getPrimaryColor();
            $userResponse['secondary_color'] =$user->getFacility()->getSecondaryColor();
            $userResponse['url_prefix'] =$user->getFacility()->getUrlPrefix();
        }
        return $userResponse;
    }

    /**
     * @return \Traversable
     */
    public function getAllUsers()
    {
        return $this->userManager->findUsers();
    }

    /**
     * @param User[] $users
     * @return array
     */
    public function makeUsersResponse($users)
    {
        $returnResponse = [];
        foreach ($users as $user) {
            $returnResponse[] = $this->makeSingleUserResponse($user);
        }

        return $returnResponse;
    }

    /**
     * @param User|null $user
     * @return array
     */
    public function makeSingleUserResponse(User $user = null)
    {
        $returnResponse = [];
        if (empty($user)) {
            return $returnResponse;
        }
        $returnResponse['id'] = $user->getId();
        $returnResponse['first_name'] = $user->getFirstName();
        $returnResponse['last_name'] = $user->getLastName();
        $returnResponse['username'] = $user->getUsername();
        $returnResponse['email'] = $user->getEmail();
        $returnResponse['roles'] = $user->getRoles();
        $returnResponse['phone'] = $user->getPhone();
        $returnResponse['dob'] = $user->getDob();
        $returnResponse['gender'] = $user->getGender();
        $returnResponse['created'] = $user->getCreated() ? $user->getCreated()->format(CommonEnum::DATE_FORMAT) : null;

        return $returnResponse;
    }

    /**
     * @param $phone
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    public function findUserByPhone($phone)
    {
        return $this->userManager->findUserBy(['phone' => $phone]);
    }

    /**
     * @param array $data
     * @return \FOS\UserBundle\Model\UserInterface|mixed
     */
    public function createOrganizaionUser(array $data)
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(StatusEnum::ACTIVE);
        $user->setUsername($data['username']);
        $user->setEmail($data['contact_email']);
        $encoded = $this->encoder->encodePassword($user, $data['password']);
        $user->setPassword($encoded);
        $user->setRoles([UserEnum::ROLE_ORGANIZATION]);
        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param $data
     * @param $user
     * @return \FOS\UserBundle\Model\UserInterface|mixed
     */
    public function updateOrganizaionUser(array $data, User $user)
    {
        if (isset($data['username']) && $user->getUsername() != $data['username']) {
            $user->setUsername($data['username']);
        }
        if (isset($data['contact_email']) && $user->getEmail() != $data['contact_email']) {
            $user->setEmail($data['contact_email']);
        }

        if (isset($data['password'])) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $validPassword = $encoder->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt());
            if (!$validPassword) {
                $encoded = $this->encoder->encodePassword($user, $data['password']);
                $user->setPassword($encoded);
            }
        }

        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param $phone
     * @return \FOS\UserBundle\Model\UserInterface|mixed
     */
    public function createUser($phone)
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(StatusEnum::ACTIVE);
        $user->setUsername($phone);
        $user->setPhone($phone);
        $user->setEmail($phone . "@email.com");
        $user->setPlainPassword(time());
        $user->setRoles([UserEnum::ROLE_CLIENT]);

        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user)
    {
        $user->setEnabled(StatusEnum::INACTIVE);
        $this->userManager->updateUser($user);
    }

    /**
     * @return string
     */
    public function getVerificationCode()
    {
        return sprintf("%05d", mt_rand(1, 99999));
    }

    /**
     * @param $user
     */
    public function updateUser($user)
    {
        $this->userManager->updateUser($user);
    }

    /**
     * @param User $user
     * @return array|bool
     * @throws \Exception
     */
    public function getAccessToken(User $user)
    {
        $client = $this->clientRepository->findOneBy([]);

        if (!$client instanceof Client) {
            return false;
        }

        $accessToken = $this->accessTokenManager->createToken();
        $accessToken->setUser($user);
        $accessToken->setExpiresAt(null);
        $token = $this->genAccessToken();
        $accessToken->setToken($token);
        $accessToken->setClient($client);
        $this->accessTokenManager->updateToken($accessToken);
        return [
            'token' => $token
        ];
    }

    /**
     * This function is taken from oAuth2 service
     * @return string
     */
    private function genAccessToken()
    {
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100);
        } elseif (function_exists('openssl_random_pseudo_bytes')) { // Get 100 bytes of pseudo-random data
            $bytes = openssl_random_pseudo_bytes(100, $strong);
            if (true === $strong && false !== $bytes) {
                $randomData = $bytes;
            }
        }

        // Last resort: mt_rand
        if (empty($randomData)) { // Get 108 bytes of (pseudo-random, insecure) data
            $randomData = mt_rand() . mt_rand() . mt_rand() . uniqid(mt_rand(), true) . microtime(true) . uniqid(
                    mt_rand(),
                    true
                );
        }

        return rtrim(strtr(base64_encode(hash('sha256', $randomData)), '+/', '-_'), '=');
    }

    /**
     * @param $userId
     * @return User|null
     */
    public function getUserById($userId)
    {
        return $this->userRepository->find($userId);
    }
}
