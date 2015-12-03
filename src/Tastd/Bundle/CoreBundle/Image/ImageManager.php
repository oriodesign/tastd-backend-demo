<?php

namespace Tastd\Bundle\CoreBundle\Image;

use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\ImageWorkshop;

/**
 * Class ImageManager
 *
 * @package Tastd\Bundle\CoreBundle\Image
 */
class ImageManager
{

    /**
     * @param $data
     * @param $width
     * @param $height
     * @return resource
     *
     * @throws \PHPImageWorkshop\Exception\ImageWorkshopException
     */
    public function resizeData($data, $width, $height)
    {
        $layer = $this->initFromData($data);
        $layer = $this->resizeRectangle($layer, $width, $height);
        $image = $layer->getResult('FFF');

        return $this->imageToStream($image);
    }

    protected function imageToStream($image)
    {
        ob_start(); // start a new output buffer
        imagejpeg( $image, NULL, '70');
        $resultData = ob_get_contents();
        ob_end_clean();

        return $resultData;
    }

    /**
     * @param $data
     * @return ImageWorkshopLayer
     *
     * @throws \PHPImageWorkshop\Exception\ImageWorkshopException
     */
    protected function initFromData($data)
    {
        return ImageWorkshop::initFromString($data);
    }

    /**
     * @param $data
     * @return ImageWorkshopLayer
     *
     * @throws \PHPImageWorkshop\Exception\ImageWorkshopException
     */
    protected function initFromDataWithTempFile($data)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'img');
        $handle = fopen($tempFile, 'w+');
        fwrite($handle, $data);

        return ImageWorkshop::initFromPath($tempFile);
    }

    /**
     * @param ImageWorkshopLayer $layer
     * @param int                $expectedWidth
     * @param int                $expectedHeight
     * @return ImageWorkshopLayer
     */
    protected function resizeRectangle(ImageWorkshopLayer $layer, $expectedWidth, $expectedHeight)
    {
        // Determine the largest expected side automatically
        ($expectedWidth > $expectedHeight) ? $largestSide = $expectedWidth : $largestSide = $expectedHeight;

        if (!$this->canCropInside($layer, $expectedWidth, $expectedHeight)) {
            // Get a squared layer
            $layer->cropMaximumInPixel(0, 0, "MM");
        }

        // Resize the layer with the largest side of the expected thumb
        $layer->resizeInPixel($largestSide, $largestSide, true);

        // Crop the layer to get the expected dimensions
        $layer->cropInPixel($expectedWidth, $expectedHeight, 0, 0, 'MM');

        return $layer;
    }

    /**
     * Check if the original image can contain the thumb without changing ratio
     *
     * @param ImageWorkshopLayer $layer
     * @param int $width
     * @param int $height
     *
     * @return bool
     */
    protected function canCropInside(ImageWorkshopLayer $layer, $width, $height)
    {
        $originalImageIsHigher = ($layer->getWidth() / $layer->getHeight()) < ($width / $height);

        if ($this->isHorizontal($layer)) {
            return $originalImageIsHigher;
        }

        return !$originalImageIsHigher;
    }

    /**
     * @param ImageWorkshopLayer $layer
     *
     * @return bool
     */
    protected function isHorizontal(ImageWorkshopLayer $layer)
    {
        return $layer->getWidth() >= $layer->getHeight();
    }

}