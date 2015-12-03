<?php

namespace Tastd\Bundle\CoreBundle\Exception\Util;

use  FOS\RestBundle\Util\ExceptionWrapper as BaseWrapper;
use Symfony\Component\Form\FormInterface;

/**
 * Class ExceptionWrapper
 *
 * @package Tastd\Bundle\CoreBundle\Exception\Util
 */
class ExceptionWrapper extends BaseWrapper
{
    private $id;
    private $code;
    private $category;
    private $info;
    private $message;
    private $errors;

    /**
     * @param array $data
     */
    public function __construct($data)
    {

        $this->code = $data['status_code'];
        $this->message = $data['message'];

        if (isset($data['errors'])) {
            $this->errors = $data['errors'];
        }

        /**
         * Extra info for ApiExceptions
         */
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }

        if (isset($data['category'])) {
            $this->category = $data['category'];
        }

        if (isset($data['info'])) {
            $this->info = $data['info'];
        }



    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return FormInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
