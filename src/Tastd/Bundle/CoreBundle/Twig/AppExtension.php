<?php

namespace Tastd\Bundle\CoreBundle\Twig;

/**
 * Class AppExtension
 *
 * @package Tastd\Bundle\CoreBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    /** @var string */
    protected $cloudFrontUrl;

    /**
     * @param string $cloudFrontUrl
     */
    public function setCloudFrontUrl ($cloudFrontUrl)
    {
        $this->cloudFrontUrl = $cloudFrontUrl;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cloudfront', array($this, 'cloudfrontFilter')),
        );
    }

    /**
     * @param $src
     * @return string
     */
    public function cloudfrontFilter($src)
    {
        return $this->cloudFrontUrl . $src;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}