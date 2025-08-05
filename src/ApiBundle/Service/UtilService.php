<?php

namespace App\ApiBundle\Service;

use App\Entity\User;
use App\Repository\ResetTokenRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class UtilService
 * @package App\ApiBundle\Service
 */
class UtilService
{
    CONST USERNAME_PRE_FIX = 'user-';
    CONST EMAIL_PRE_FIX = 'sample@gmail.com';

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var ValidatorInterface $validator */
    private $validator;

    /** @var ParameterBagInterface $params */
    private $params;

    /** @var ResetTokenRepository  */
    private $resetTokenRepository;

    /**
     * UtilService constructor.
     * @param TranslatorInterface $translator
     * @param ParameterBagInterface $params
     * @param ValidatorInterface $validator
     * @param ResetTokenRepository $resetTokenRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        ParameterBagInterface $params,
        ValidatorInterface $validator,
        ResetTokenRepository $resetTokenRepository
    ) {
        $this->translator = $translator;
        $this->validator = $validator;
        $this->params = $params;
        $this->resetTokenRepository = $resetTokenRepository;
    }

    /**
     * @param integer $statusCode
     * @param null $message
     * @param null $data
     * @return JsonResponse
     */
    public function makeResponse($statusCode, $message = null, $data = null)
    {
        $response['status_code'] = $statusCode;
        if ($message) {
            $response['message'] = $this->translator->trans($message);
        }
        if (is_array($data)) {
            $response['data'] = $data;
        }
        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param array $data
     * @param array $fields
     * @return array|bool
     */
    public function checkRequiredFieldsByRequestedData($data, array $fields)
    {
        $valuesArray = [];
        foreach ($fields as $field) {
            $value = isset($data[$field]) ? $data[$field] : null;
            if ($value === null) {
                return false;
            }
            $valuesArray[$field] = $value;
        }
        return $valuesArray;
    }

    /**
     * @param $data
     * @param $possibleFields
     * @return bool
     */
    public function checkIfRequestHasFieldsToUpdate($data, $possibleFields)
    {
        foreach ($possibleFields as $field) {
            if (isset($data[$field])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getProjectRootDir()
    {
        return $this->params->get('kernel.project_dir');
    }

    /**
     * @param $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->params->get($parameter);
    }

    /**
     * @param User $user
     * @return \App\Entity\ResetToken
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function makeUserResetToken(User $user)
    {
        $expiry = new \DateTime('+1 hour');
        $token = rand(10, 99) . time();

        return $this->resetTokenRepository->addUserRestToken($user, $expiry, $token);
    }

    /**
     * @param $zipCode
     * @return bool
     */
    public function getLatLngByZipCode($zipCode)
    {
        $googleApiKey = $this->getParameter('GOOGLE_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zipCode)."&key=$googleApiKey";
        $resultString = file_get_contents($url);
        $result = json_decode($resultString, true);
        if(empty($result) || empty($result['results'])) {
            return [];
        }
        $result1[] = $result['results'][0];
        $result2[] = $result1[0]['geometry'];
        $result3[] = $result2[0]['location'];
        return $result3[0];
    }

    /**
     * @param array $data
     * @return string
     */
    public function getDummyUsername($data) {
        $username = isset($data['first_name']) ? str_replace(" ", "", $data['first_name'] . '-') : '';
        $username .= isset($data['last_name']) ? str_replace(" ", "", $data['last_name'] . '-') : '';
        $username .= self::USERNAME_PRE_FIX . time();

        return $username;
    }

    public function getDummmyEmail($data) {
        $email = isset($data['first_name']) ? str_replace(" ","", $data['first_name'] . '-') : '';
        $email .= isset($data['last_name']) ? str_replace(" ","", $data['last_name'] . '-') : '';
        $email .= isset($data['date_of_incident']) ? explode(" ", $data['date_of_incident'])[0] . '-' : '';
        $email .= self::EMAIL_PRE_FIX;

        return $email;
    }

    /**
     * @param UploadedFile $file
     * @param string $dir
     * @return string
     */
    public function moveFile(UploadedFile $file, string $dir)
    {
        $filePath = $this->getProjectRootDir() . $dir;
        $realFilePath = realpath($filePath);
        if (!$realFilePath) {
            mkdir($filePath, 0777, true);
        }
        $realFilePath = realpath($filePath);

        $fileName = rand(0, 999) . time() . '.' . $file->guessExtension();
        $file->move($realFilePath, $fileName);

        return $dir . '/' . $fileName;
    }

    /**
     * @param $image
     * @param $width
     * @param $height
     * @return bool
     */
    public function verifyImageDimension($image, $width, $height)
    {
        $dimensionsInfo = getimagesize($image);
        if ($dimensionsInfo[0] <= $width && $dimensionsInfo[1] <= $height) {
            return true;
        }
        return false;
    }

    /**
     * @param UploadedFile $image
     * @param array $dimensionConstraints
     * @return bool
     */
    public function isValidFile(UploadedFile $image, $dimensionConstraints = [])
    {
        $constraints = ['maxSize' => '15M', 'mimeTypes' => ['image/jpeg', 'image/png', 'video/mp4']];

        if (!empty($dimensionConstraints)) {
            $constraints = array_merge($constraints, $dimensionConstraints);
        }

        $imageConstraint = new Image($constraints);
        $errorList = $this->validator->validate($image, $imageConstraint);

        if (count($errorList)) {
            return $errorList[0]->getMessage();
        }

        return true;
    }

    /**
     * @param UploadedFile $image
     * @param array $dimensionConstraints
     * @return bool
     */
    public function isValidImage(UploadedFile $image, $dimensionConstraints = [])
    {
        $constraints = ['maxSize' => '15M', 'mimeTypes' => ['image/jpeg', 'image/png']];

        if (!empty($dimensionConstraints)) {
            $constraints = array_merge($constraints, $dimensionConstraints);
        }

        $errorList = $this->validator->validate($image, new Image($constraints));

        if (count($errorList)) {
            return $errorList[0]->getMessage();
        }

        return true;
    }

    /**
     * @param UploadedFile $mediaFile
     * @param array $dimensionConstraints
     * @return bool
     */
    public function isValidMedia(UploadedFile $mediaFile, $dimensionConstraints = [])
    {
        $constraints = ['maxSize' => '50M', 'mimeTypes' => ['video/mp4', 'audio/mpeg']];

        if (!empty($dimensionConstraints)) {
            $constraints = array_merge($constraints, $dimensionConstraints);
        }

        $errorList = $this->validator->validate($mediaFile, new File($constraints));

        if (count($errorList)) {
            return $errorList[0]->getMessage();
        }

        return true;
    }
}
