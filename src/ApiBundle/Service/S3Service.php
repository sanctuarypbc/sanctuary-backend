<?php

namespace App\ApiBundle\Service;

use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class S3Service
 * @package App\ApiBundle\Service
 */
class S3Service
{
    const SIGNED_URL_VALIDITY = '+10 minutes';

    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * S3Service constructor.
     * @param S3Client $client
     * @param ParameterBagInterface $params
     */
    public function __construct(S3Client $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->bucket = $params->get('s3-bucket');
    }
    /**
     * @param string $fileName
     * @param string $content
     * @param array  $meta
     * @param string $privacy
     * @return string file url
     */
    public function upload($fileName, $content, array $meta = [], $privacy = 'private')
    {
        $fileObj = $this->client->upload($this->bucket, $fileName, $content, $privacy, [
            'Metadata' => $meta,
        ]);
        return $fileObj->toArray()['ObjectURL'];
    }

    /**
     * @param $file
     * @param string $prefix
     * @return string
     */
    public function uploadFile($file, $prefix = "")
    {
        $meta['contentType'] = $file->getClientMimeType();
        $fileName = $prefix . rand(0, 999) . time() . "." . $file->guessExtension();
        $this->upload($fileName, file_get_contents($file->getPathname()), $meta);

        return $fileName;
    }
    /**
     * @param $fileName
     * @return bool
     */
    public function fileExist($fileName)
    {
        return $this->client->doesObjectExist($this->bucket, $fileName);
    }
    /**
     * @param $fileName
     * @return \Aws\Result
     */
    public function deleteFile($fileName)
    {
        return $this->client->deleteObject(array('Bucket' => $this->bucket, 'Key' => $fileName));
    }

    /**
     * @param $directoryName
     */
    public function deleteFilesInDirectory($directoryName)
    {
        $this->client->deleteMatchingObjects($this->bucket, $directoryName. '/');
    }
    /**
     * @param $fileName
     * @return string
     */
    public function getFileUrl($fileName)
    {
        return $this->client->getObjectUrl($this->bucket, $fileName);
    }
    /**
     * @param $targetKeyname
     * @param $sourceKeyname
     */
    public function copyFile($targetKeyname, $sourceKeyname)
    {
        $this->client->copyObject(array(
            'Bucket' => $this->bucket,
            'Key' => $targetKeyname,
            'CopySource' => "{$this->bucket}/{$sourceKeyname}",
            'ACL' => 'private'
        ));
    }

    /**
     * @param $sourcePath
     * @param $targetPath
     * @return \Aws\Result
     */
    public function downloadFile($sourcePath, $targetPath)
    {
        return $this->client->getObject(array(
            'Bucket' => $this->bucket,
            'Key' => $sourcePath,
            'SaveAs' => $targetPath
        ));
    }

    /**
     * @param $path
     * @return bool
     */
    public function isDirectoryExist($path)
    {
        $result = $this->client->listObjectsV2(array(
            'Bucket' => $this->bucket,
            'Prefix' => $path
        ));

        if (!empty($result['KeyCount']) && $result['KeyCount'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $fileName
     * @param $originalName
     * @return string
     */
    public function getSignedUrl($fileName, $originalName = "")
    {
        if (empty($originalName)) {
            $originalName = basename($fileName);
        }

        $command = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $fileName,
            'ResponseContentDisposition' => 'attachment; filename ="' . $originalName . '"'
        ]);
        $request = $this->client->createPresignedRequest($command, self::SIGNED_URL_VALIDITY);
        return (string) $request->getUri();
    }
}
