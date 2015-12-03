<?php

namespace Tastd\Bundle\CoreBundle\Entity\Formatter;

/**
 * Interface FormatterInterface
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Formatter
 */
interface EntityFormatterInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * @param mixed $entity
     *
     * @return string
     */
    public function getOneLineDescription($entity);

    /**
     * @param mixed $entity
     *
     * @return string
     */
    public function getOneLineShortDescription($entity);
}