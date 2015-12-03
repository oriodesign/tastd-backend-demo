<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use RMS\PushNotificationsBundle\Service\Notifications;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Wrep\Notificato\Notificato;

/**
 * Class CuisineColorCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class PushMessageCommand extends Command
{
    /** @var Notifications */
    protected $notifications;
    /** @var Notificato */
    protected $notificato;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;

    public function __construct(Notifications $notifications, Notificato $notificato)
    {
        parent::__construct();
        $this->notifications = $notifications;
        $this->notificato = $notificato;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:push:message')
            ->setDescription('Push Message to user')
            ->addArgument('device', InputArgument::REQUIRED, '')
            ->addArgument('message', InputArgument::REQUIRED, 'Test')
            ->addOption('notificato', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        if ($input->getOption('notificato')) {
            $this->executeNotificato();
        } else {
            $this->executeRms();
        }

        $output->writeln('Push notification sent');
    }

    /**
     * executeNotificato
     */
    protected function executeNotificato()
    {
        $message = $this->input->getArgument('message');
        $device = $this->input->getArgument('device');
        $pushNotification = $this->notificato->messageBuilder()
            ->setDeviceToken($device)
            ->setBadge(1)
            ->setAlert('The message is: ' . $message)
            ->build();
        $messageEnvelope = $this->notificato->send($pushNotification);
        $this->output->writeln($messageEnvelope->getFinalStatusDescription());
    }

    /**
     * executeRms
     */
    protected function executeRms()
    {
        $message = $this->input->getArgument('message');
        $device = $this->input->getArgument('device');
        $pushNotification = new iOSMessage();
        $pushNotification->setMessage($message);
        $pushNotification->setDeviceIdentifier($device);
        $this->notifications->send($pushNotification);
    }




}