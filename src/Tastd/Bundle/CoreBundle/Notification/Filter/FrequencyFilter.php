<?php

namespace Tastd\Bundle\CoreBundle\Notification\Filter;
use Tastd\Bundle\CoreBundle\Repository\PushMessageRepository;

/**
 * Class FrequencyFilter
 *
 * @package Tastd\Bundle\CoreBundle\Notification\Filter
 */
class FrequencyFilter
{

    /** @var array */
    protected $pushedUsers;
    /** @var PushMessageRepository */
    protected $pushMessageRepository;

    /**
     * @param PushMessageRepository $pushMessageRepository
     */
    public function __construct(PushMessageRepository $pushMessageRepository)
    {
        $this->pushMessageRepository = $pushMessageRepository;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function filter($userId)
    {
        if (!isset($this->pushedUsers)) {
            $this->pushedUsers = $this->pushMessageRepository->getRecentlyPushedUsersIds();
        }

        if (in_array($userId, $this->pushedUsers)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userId
     */
    public function addPushedUser($userId)
    {
        $this->pushedUsers[] = $userId;
    }

}