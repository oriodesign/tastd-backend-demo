<?php

namespace Tastd\Bundle\CoreBundle\Manager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContext;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class UserMetaManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class UserMetaManager
{

    protected $cloudfrontUrl;
    protected $userRepository;
    protected $securityContext;

    /**
     * @param UserRepository  $userRepository
     * @param SecurityContext $securityContext
     */
    public function __construct(
        UserRepository $userRepository,
        SecurityContext $securityContext)
    {
        $this->userRepository = $userRepository;
        $this->securityContext = $securityContext;
    }


    /**
     * @param User $user
     */
    public function hydrateIsMyLeader(User $user)
    {
        $isMyLeader = in_array(
            $user->getId(),
            $this->userRepository->getLeadersOf($this->securityContext->getToken()->getUser()->getId())
        );
        $user->setIsMyLeader($isMyLeader);
    }

    /**
     * @param User $user
     */
    public function hydrateIsMyFollower(User $user)
    {
        $isMyLeader = in_array(
            $user->getId(),
            $this->userRepository->getFollowersOf($this->securityContext->getToken()->getUser()->getId())
        );
        $user->setIsMyFollower($isMyLeader);
    }

}
