<?php

namespace Tastd\Bundle\CoreBundle\Exception\Api;

/**
 * Interface ApiExceptionInterface
 *
 * @package Tastd\Bundle\CoreBundle\Exception
 */
abstract class AbstractApiException extends \Exception implements ApiExceptionInterface
{
    protected $id;
    protected $code;
    protected $category;
    protected $message;
    protected $info;
    protected $errors;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }







    /**
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return array();
    }
}