<?php

namespace Tastd\Bundle\CoreBundle\Aws;

use Aws\S3\S3Client as BaseS3Client;
use Guzzle\Http\Url;
use Guzzle\Service\Resource\Model;
use Tastd\Bundle\CoreBundle\Image\ImageManager;

/**
 * Class S3Client
 *
 * @package Tastd\Bundle\CoreBundle\Aws
 */
class S3Client
{
    protected $s3;
    protected $bucket;
    protected $imageManager;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $bucket
     * @param ImageManager $imageManager
     */
    public function __construct($apiKey, $apiSecret, $bucket, $imageManager)
    {
        $this->bucket = $bucket;
        $this->imageManager = $imageManager;
        $this->s3 = BaseS3Client::factory(array(
            'key' => $apiKey,
            'secret' => $apiSecret
        ));
    }

    /**
     * @return Model
     */
    public function listBuckets()
    {
        return $this->s3->listBuckets();
    }

    /**
     * @param string $url
     * @param string $prefix
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public function uploadFromUrl($url, $prefix = 'avatar/', $width = null, $height = null)
    {
        $urlData = parse_url($url);
        $fileData = file_get_contents($url);

        return $this->uploadData($fileData, $prefix, pathinfo($urlData['path'], PATHINFO_EXTENSION), $width, $height);
    }

    /**
     * @param string $base64Data
     * @param string $prefix
     * @param string $extension
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public function uploadBase64($base64Data, $prefix = 'avatar/', $extension = 'jpg', $width = null, $height = null)
    {
        $data = base64_decode($base64Data);
        $resource = finfo_open();
        $mime_type = finfo_buffer($resource, $data, FILEINFO_MIME_TYPE);
        $map = array(
            'image/gif'         => 'gif',
            'image/jpeg'        => 'jpg',
            'image/png'         => 'png'
        );

        if ($mime_type && isset($map[$mime_type])) {
            $extension = $map[$mime_type];
        }

        return $this->uploadData($data, $prefix, $extension, $width, $height);
    }

    /**
     * @param string $fileData
     * @param string $prefix
     * @param string $extension
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public function uploadData ($fileData, $prefix = 'avatar/', $extension = 'jpg', $width = null, $height = null)
    {
        if ($width && $height) {
            $fileData = $this->imageManager->resizeData($fileData, $width, $height);
        }

        $filename = $this->generateS3Filename($prefix, $extension);
        $this->doUpload($fileData, $filename);

        return $filename;
    }

    protected function doUpload($fileData, $filename)
    {
        $result = $this->s3->putObject(array(
            'Bucket'  => $this->bucket,
            'Key'     => $filename,
            'Body'    => $fileData,
            'Metadata'   => array()
        ));
        $this->s3->waitUntil('ObjectExists', array(
            'Bucket' => $this->bucket,
            'Key'    => $filename
        ));
    }

    /**
     * @param string $prefix
     * @param string $extension
     *
     * @return string
     */
    protected function generateS3Filename($prefix = 'avatar/', $extension = '.bin')
    {
        return $prefix . uniqid() . '.' . $extension;
    }

}