<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Event\UserCreatedEvent;
use Tastd\Bundle\CoreBundle\Mailer\Mailer;
use Tastd\Bundle\CoreBundle\Repository\ConversionRepository;

/**
 * Class CreateUserListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class CreateUserListener
{

    protected $mailer;
    protected $conversionRepository;
    protected $entityManager;

    /**
     * @param EntityManager        $entityManager
     * @param Mailer               $mailer
     * @param ConversionRepository $conversionRepository
     */
    public function __construct(EntityManager $entityManager, Mailer $mailer, ConversionRepository $conversionRepository)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->conversionRepository = $conversionRepository;
    }

    /**
     * @param UserCreatedEvent $userCreatedEvent
     */
    public function onUserCreated(UserCreatedEvent $userCreatedEvent)
    {
        $user = $userCreatedEvent->getUser();
        $this->mailer->sendWelcomeEmail($user);
        $this->updateConversion($userCreatedEvent);
    }

    /**
     * @param UserCreatedEvent $userCreatedEvent
     */
    protected function updateConversion(UserCreatedEvent $userCreatedEvent)
    {
        $fingerprint = $userCreatedEvent->getFingerprint();
        $user = $userCreatedEvent->getUser();
        if (null === $fingerprint) {
            return;
        }
        $conversion = $this->conversionRepository->getLastByFingerprint($fingerprint);
        if (null === $conversion) {
            return;
        }
        $conversion->setUser($user);
        $this->entityManager->flush();
    }
}