<?php

namespace Tastd\Bundle\CoreBundle\Notification;

use Tastd\Bundle\CoreBundle\Entity\Notification;

/**
 * Class NotificationFactoryBag
 *
 * @package Tastd\Bundle\CoreBundle\Notification
 */
class NotificationFactoryBag
{
    /** @var NotificationFactory[]  */
    protected $factories;

    public function __construct()
    {
        $this->factories = array();
    }

    /**
     * @param $factory
     */
    public function addNotificationFactory(NotificationFactory $factory)
    {
        $this->factories[] = $factory;
    }

    /**
     * @param array $data
     *
     * @return Notification
     * @throws \Exception
     */
    public function create($data)
    {
        $notifications = array();
        foreach ($this->factories as $factory) {
            if ($factory->getEventName() === $data['event']) {
                $notifications[] = $factory->create($data);
            }
        }

        if (count($notifications)===0) {
            throw new \Exception('NotificationFactory not found for event ' .  $data['event']);
        }

        return $notifications;
    }

}