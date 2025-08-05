<?php

namespace App\ApiBundle\Service;

use App\Entity\Newsfeed;
use App\Entity\NewsfeedFile;
use App\Entity\User;
use App\Enum\NewsfeedEnum;
use App\Enum\UserEnum;
use App\Repository\NewsfeedFileRepository;
use App\Repository\NewsfeedRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class NewsfeedService
 * @package App\ApiBundle\Service
 */
class NewsfeedService
{
    /** @var NewsfeedRepository  */
    private $newsfeedRepository;

    /** @var NewsfeedFileRepository  */
    private $newsfeedFileRepository;

    /** @var S3Service  */
    private $s3Service;

    /** @var UtilService  */
    private $utilService;

    /**
     * NewsfeedService constructor.
     * @param NewsfeedRepository $newsfeedRepository
     * @param NewsfeedFileRepository $newsfeedFileRepository
     * @param S3Service $s3Service
     * @param UtilService $utilService
     */
    public function __construct(
        NewsfeedRepository $newsfeedRepository,
        NewsfeedFileRepository  $newsfeedFileRepository,
        S3Service $s3Service,
        UtilService $utilService
    ) {
        $this->newsfeedRepository = $newsfeedRepository;
        $this->newsfeedFileRepository = $newsfeedFileRepository;
        $this->s3Service = $s3Service;
        $this->utilService = $utilService;
    }

    /**
     * @param $data
     * @param $images
     * @param $videos
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNewsfeed($data, $files)
    {
        $showTOClientNewsFeedCount = $this->newsfeedRepository->getCountByShowToClient();
        $newsfeed = new Newsfeed();
        $this->newsfeedRepository->persist($newsfeed);
        if (!empty($data)) {
            $showTOClientNewsFeedCount >= NewsfeedEnum::MAX_SHOW_TO_CLIENT ? $data['show_to_client'] = false : null;
            foreach (NewsfeedEnum::POSSIBLE_FIELDS as $parameter => $field) {
                $setterFun = "set" . $field;
                isset($data[$parameter]) ? $newsfeed->$setterFun($data[$parameter]) : null;
            }
        }

        if (!empty($files) && is_array($files)) {
            foreach ($files as $file) {
                $response = $this->uploadToS3AndSavePath($newsfeed, $file);

                if ($response !== true) {
                    return $response;
                }
            }
        }

        $this->newsfeedRepository->flush();
        return true;
    }

    /**
     * @param Newsfeed $newsfeed
     * @param UploadedFile $file
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function uploadToS3AndSavePath(Newsfeed $newsfeed, UploadedFile $file)
    {
        $response = $this->utilService->isValidFile($file);
        if ($response !== true) {
            return $response;
        }
        $s3Dir = in_array($file->getMimeType(), ['image/jpeg', 'image/png']) ? NewsfeedEnum::S3_IMAGES_DIR : NewsfeedEnum::S3_VIDEOS_DIR;
        $fileType = in_array($file->getMimeType(), ['image/jpeg', 'image/png']) ? NewsfeedEnum::FILE_TYPE_IMAGE : NewsfeedEnum::FILE_TYPE_VIDEO;
        $fileName = $this->s3Service->uploadFile($file, $s3Dir);
        $this->newsfeedFileRepository->create($newsfeed, $fileName, $fileType);

        return true;
    }

    /**
     * @param int $newsfeedId
     * @param $data
     * @param $files
     * @return bool|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateNewsfeed(int $newsfeedId, $data, $files)
    {
        $newsfeedObj = $this->newsfeedRepository->findOneBy(['id' => $newsfeedId, 'status' => NewsfeedEnum::STATUS_ACTIVE]);
        if (!$newsfeedObj instanceof Newsfeed) {
            return "Newsfeed doesn't exist.";
        }

        $showTOClientNewsFeedCount = $this->newsfeedRepository->getCountByShowToClient();
        if (!empty($data)) {
            if (isset($data['show_to_client']) && $data['show_to_client'] == true && $showTOClientNewsFeedCount >= NewsfeedEnum::MAX_SHOW_TO_CLIENT) {
                return NewsfeedEnum::MAX_SHOW_TO_CLIENT . " newsfeeds are already selected for client screen.";
            }

            foreach (NewsfeedEnum::POSSIBLE_FIELDS as $parameter => $field) {
                $setterFun = "set" . $field;
                isset($data[$parameter]) ? $newsfeedObj->$setterFun($data[$parameter]) : null;
            }
        }

        if (!empty($files) && is_array($files)) {
            foreach ($files as $file) {
                $response = $this->uploadToS3AndSavePath($newsfeedObj, $file);

                if ($response !== true) {
                    return $response;
                }
            }
        }

        $this->newsfeedRepository->flush();
        return true;
    }

    /**
     * @param int $newsfeedId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteNewsfeed(int $newsfeedId)
    {
        $newsfeedObj = $this->newsfeedRepository->findOneBy(['id' => $newsfeedId, 'status' => NewsfeedEnum::STATUS_ACTIVE]);
        if (!$newsfeedObj instanceof Newsfeed) {
            return "Newsfeed doesn't exist.";
        }

        $newsfeedObj->setStatus(NewsfeedEnum::STATUS_DELETED);
        $newsfeedObj->setShowToClient(NewsfeedEnum::STATUS_DELETED);
        $this->newsfeedRepository->flush();

        return true;
    }

    /**
     * @param int $newsfeedFileId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteNewsfeedFile(int $newsfeedFileId)
    {
        $newsfeedFileObj = $this->newsfeedFileRepository->find($newsfeedFileId);
        if (!$newsfeedFileObj instanceof NewsfeedFile) {
            return "Newsfeed file doesn't exist.";
        }

        $this->s3Service->deleteFile($newsfeedFileObj->getPath());
        $this->newsfeedFileRepository->remove($newsfeedFileObj, true);

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getNewsfeeds(User $user, array $data)
    {
        $showToClientNewsfeedsOnly = false;
        if (in_array(UserEnum::ROLE_CLIENT, $user->getRoles())) {
            $showToClientNewsfeedsOnly = true;
        }

        $returnArray = [];
        $newsfeeds = $this->newsfeedRepository->getNewsfeeds($data, $showToClientNewsfeedsOnly);
        foreach ($newsfeeds as $newsfeed) {
            $filesDataArray = [];
            $newsfeedFiles = $this->newsfeedFileRepository->getNewsfeedFiles($newsfeed['id']);
            foreach ($newsfeedFiles as $newsfeedFile) {
                $newsfeedFile['path'] = $this->s3Service->getSignedUrl($newsfeedFile['path']);
                $filesDataArray[] = $newsfeedFile;
            }
            $newsfeed['files'] = $filesDataArray;
            $returnArray[] = $newsfeed;
        }

        return $returnArray;
    }
}
