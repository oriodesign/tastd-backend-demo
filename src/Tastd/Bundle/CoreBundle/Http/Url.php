<?php

namespace Tastd\Bundle\CoreBundle\Http;

/**
 * Class Url
 *
 * @package Tastd\Bundle\Http
 */
class Url
{

    protected $url;
    protected $queryParameters;

    /**
     * @param string $url
     * @param array  $queryParameters
     */
    public function __construct($url, $queryParameters = array())
    {
        $this->url = $url;
        $this->queryParameters = $queryParameters;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $url = $this->url . http_build_query($this->queryParameters, null, '&', PHP_QUERY_RFC3986);
        $url = str_replace('%2C', ',', $url); // @TODO problem with comma in url
        $url = str_replace('%7C', '|', $url);
        $url = str_replace('%27', '%60', $url); // @TODO Convert manually from ' to ` for google api

        return $url;
    }

}