<?php

namespace App\ApiBundle\Service;

use App\Entity\User;
use App\Entity\WellnessTip;
use App\Enum\UserEnum;
use App\Enum\WellnessTipEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class RequestService
 * @package App\ApiBundle\Service
 */
class WellnessTipService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UtilService */
    private $utilService;

    /**
     * @var S3Service
     */
    private $s3Service;

    /**
     * WellnessTipService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UtilService $utilService
     * @param S3Service $s3Service
     */
    public function __construct(EntityManagerInterface $entityManager, UtilService $utilService, S3Service $s3Service)
    {
        $this->entityManager = $entityManager;
        $this->utilService = $utilService;
        $this->s3Service = $s3Service;
    }

    /**
     * @param User $user
     * @param array $data
     * @param UploadedFile $wellnessTipImage
     * @param UploadedFile $wellnessTipMedia
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return mixed
     */
    public function createWellnessTip( User $user, array $data, $wellnessTipImage, $wellnessTipMedia)
    {
        $response = [];
        $response[WellnessTipEnum::MESSAGE] = "WellnessTip Created!";
        $response[WellnessTipEnum::STATUS] = true;

        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            $response [WellnessTipEnum::MESSAGE] = WellnessTipEnum::ERROR_MESSAGE_INVALID_ROLE;
            $response[WellnessTipEnum::STATUS] = false;

            return $response;
        }
        $wellnessTip = new WellnessTip();
        $wellnessTip->setHeading($data[WellnessTipEnum::PARM_HEADING]);
        $wellnessTip->setBody($data[WellnessTipEnum::PARM_BODY]);
        $wellnessTip->setIcon($data[WellnessTipEnum::PARM_ICON]);
        $wellnessImageSource = !empty($wellnessTipImage) ? $this->uploadImageToS3($wellnessTipImage) : null;
        $wellnessMediaSource = !empty($wellnessTipMedia) ? $this->uploadMediaToS3($wellnessTipMedia) : null;
        $wellnessTip->setImage($wellnessImageSource);
        $wellnessTip->setMedia($wellnessMediaSource);

        $this->entityManager->persist($wellnessTip);
        $this->entityManager->flush();

        return $response;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getWellnessTip($data)
    {
        $wellnessTips = $this->entityManager->getRepository('App:WellnessTip')->getWellnessTip($data);
        $response = [];
        foreach ($wellnessTips as $wellnessTip) {
            $wellnessTip[WellnessTipEnum::PARM_IMAGE] = !empty($wellnessTip[WellnessTipEnum::PARM_IMAGE]) ? $this->s3Service->getSignedUrl($wellnessTip[WellnessTipEnum::PARM_IMAGE]) : null;
            $wellnessTip[WellnessTipEnum::PARM_MEDIA] = !empty($wellnessTip[WellnessTipEnum::PARM_MEDIA]) ? $this->s3Service->getSignedUrl($wellnessTip[WellnessTipEnum::PARM_MEDIA]) : null;
            array_push($response, $wellnessTip);
        }

        return $response;
    }


    /**
     * @param User $user
     * @param int $id
     * @return bool|string
     */
    public function deleteWellnessTipById(User $user,int $id)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return WellnessTipEnum::ERROR_MESSAGE_INVALID_ROLE;
        }

        $wellnessTipObj = $this->entityManager->getRepository('App:WellnessTip')->findOneBy(['id' => $id, 'status' => WellnessTipEnum::STATUS_ACTIVE]);
        if (!$wellnessTipObj instanceof WellnessTip) {
            return WellnessTipEnum::ERROR_MESSAGE_NOT_FOUND;
        }

        $wellnessTipObj->setStatus(WellnessTipEnum::STATUS_DELETED);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $id
     * @param mixed $data
     * @param UploadedFile $wellnessTipImage
     * @param UploadedFile $wellnessTipMedia
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateWellnessTip(User $user, $id, $data, $wellnessTipImage, $wellnessTipMedia)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return WellnessTipEnum::ERROR_MESSAGE_INVALID_ROLE;
        }
        $wellnessTipObj = $this->entityManager->getRepository('App:WellnessTip')->findOneBy(['id' => $id, 'status' => WellnessTipEnum::STATUS_ACTIVE]);

        if (!$wellnessTipObj instanceof WellnessTip) {
            return WellnessTipEnum::ERROR_MESSAGE_NOT_FOUND;
        }
        if (isset($data[WellnessTipEnum::PARM_HEADING])) {
            $wellnessTipObj->setHeading($data[WellnessTipEnum::PARM_HEADING]);
        }
        if (isset($data[WellnessTipEnum::PARM_BODY])) {
            $wellnessTipObj->setBody($data[WellnessTipEnum::PARM_BODY]);
        }
        if (isset($data[WellnessTipEnum::PARM_ICON])) {
            $wellnessTipObj->setIcon($data[WellnessTipEnum::PARM_ICON]);
        }

        if ($data[WellnessTipEnum::PARM_DELETE_IMAGE] && !empty($wellnessTipObj->getImage())) {
            $this->deleteImagefromS3($wellnessTipObj->getImage());
            $wellnessTipObj->setImage(null);
        } elseif (file_exists($wellnessTipImage)) {
            $wellnessImageSource = $this->uploadImageToS3($wellnessTipImage);
            $wellnessTipObj->setImage($wellnessImageSource);
        } else {
        }

        if ($data[WellnessTipEnum::PARM_DELETE_MEDIA] && !empty($wellnessTipObj->getMedia())) {
            $this->deleteMediafromS3($wellnessTipObj->getMedia());
            $wellnessTipObj->setMedia(null);
        } elseif (file_exists($wellnessTipMedia)) {
            $wellnessMediaSource = $this->uploadMediaToS3($wellnessTipMedia);
            $wellnessTipObj->setMedia($wellnessMediaSource);
        } else {
        }

        $this->entityManager->persist($wellnessTipObj);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param UploadedFile $file
     * @return string
     */
    public function uploadImageToS3(UploadedFile $file)
    {
        $response = $this->utilService->isValidImage($file);
        if ($response !== true) {
            return $response;
        }
        return $this->s3Service->uploadFile($file, WellnessTipEnum::WELLNESSTIP_IMAGES_DIR);

    }

    /**
     * @param $fileName
     * @return string
     */
    public function deleteImagefromS3($fileName = null)
    {
        if($this->s3Service->fileExist($fileName)) {
            $this->s3Service->deleteFile($fileName);
        }
    }

    /**
     * @param $fileName
     * @return string
     */
    public function deleteMediafromS3($fileName = null)
    {
        if ($this->s3Service->fileExist($fileName)) {
            $this->s3Service->deleteFile($fileName);
        }
    }

    /**
     *
     * @param UploadedFile $file
     * @return string
     */
    public function uploadMediaToS3(UploadedFile $file)
    {
        $response = $this->utilService->isValidMedia($file);
        if ($response !== true) {
            return $response;
        }
        return $this->s3Service->uploadFile($file, WellnessTipEnum::WELLNESSTIP_MEDIA_DIR);

    }
}
