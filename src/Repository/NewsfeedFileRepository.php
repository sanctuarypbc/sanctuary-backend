<?php

namespace App\Repository;

use App\Entity\Newsfeed;
use App\Entity\NewsfeedFile;
use App\Enum\NewsfeedEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NewsfeedFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsfeedFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsfeedFile[]    findAll()
 * @method NewsfeedFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsfeedFileRepository extends AbstractRepository
{
    /**
     * NewsfeedFileRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsfeedFile::class);
    }

    /**
     * @param Newsfeed $newsfeed
     * @param string $path
     * @param string $type
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Newsfeed $newsfeed, string $path, string $type)
    {
        $newsfeedFileObj = $this->findOneBy(['newsFeed' => $newsfeed, 'status' => NewsfeedEnum::STATUS_ACTIVE]);

        if (empty($newsfeedFileObj)) {
            $newsfeedFileObj = new NewsfeedFile();
        }
        $newsfeedFileObj->setPath($path);
        $newsfeedFileObj->setType($type);
        $newsfeedFileObj->setNewsFeed($newsfeed);
        $this->persist($newsfeedFileObj, true);
    }

    /**
     * @param int $newsfeedId
     * @return int|mixed|string
     */
    public function getNewsfeedFiles(int $newsfeedId)
    {
        return $this->createQueryBuilder('nf')
            ->select('nf.id, nf.type, nf.path')
            ->where('nf.newsFeed = :newsfeedId')
            ->setParameter('newsfeedId', $newsfeedId)
            ->orderBy('nf.created', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()->getArrayResult();
    }
}
