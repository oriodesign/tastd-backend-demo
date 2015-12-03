<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api;

/**
 * Interface ApiExceptionInterface
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
interface ApiExceptionInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $message
     *
     * @return mixed
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @return mixed
     */
    public function getErrors();

    /**
     * @param array
     */
    public function setErrors($errors);

}