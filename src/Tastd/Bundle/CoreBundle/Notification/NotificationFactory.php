<?php

namespace Tastd\Bundle\CoreBundle\Notification;
use Tastd\Bundle\CoreBundle\Entity\Notification;

/**
 * Interface NotificationFactory
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
interface NotificationFactory
{
    /**
     * @param array $data
     *
     * @return Notification
     */
    public function create($data);

    /**
     * @return string
     */
    public function getEventName();

}