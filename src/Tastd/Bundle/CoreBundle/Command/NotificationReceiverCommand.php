<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tastd\Bundle\CoreBundle\Aws\SqsClient;
use Tastd\Bundle\CoreBundle\Entity\Notification;
use Tastd\Bundle\CoreBundle\Notification\NotificationDispatcher;
use Tastd\Bundle\CoreBundle\Notification\NotificationFactoryBag;

/**
 * Class NotificationReceiverCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class NotificationReceiverCommand extends Command
{
    /** @var SqsClient */
    protected $sqsClient;
    /** @var  Serializer */
    protected $serializer;
    /** @var NotificationFactoryBag  */
    protected $notificationFactoryBag;
    /** @var EntityManager  */
    protected $entityManager;
    /** @var NotificationDispatcher  */
    protected $notificationDispatcher;
    /** @var ValidatorInterface */
    protected $validator;
    /** @var OutputInterface */
    protected $output;

    public function __construct(
        SqsClient $sqsClient,
        Serializer $serializer,
        NotificationFactoryBag $notificationFactoryBag,
        EntityManager $entityManager,
        NotificationDispatcher $notificationDispatcher,
        ValidatorInterface $validator)
    {
        parent::__construct();
        $this->sqsClient = $sqsClient;
        $this->serializer = $serializer;
        $this->notificationFactoryBag = $notificationFactoryBag;
        $this->entityManager = $entityManager;
        $this->notificationDispatcher = $notificationDispatcher;
        $this->validator = $validator;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:notification:receiver')
            ->setDescription('Receiver message from queue');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(120);
        $this->output = $output;
        $loop = 0;
        while ($loop < 10) {
            $this->receiveMessages();
            sleep(10);
            $loop++;
        }
    }

    /**
     * receiveMessages
     */
    protected function receiveMessages()
    {
        $this->output->writeln('Sqs client try receiving messages');
        $messages = $this->sqsClient->receive()->get('Messages');
        if (!$messages) {
            $this->output->writeln('No messages received');
            return;
        }

        foreach ($messages as $message) {
            $this->handleMessage($message);
        }
    }

    /**
     * @param array $message
     * @throws \Exception
     */
    public function handleMessage($message)
    {
        $this->output->writeln('Handle message');
        $receiptHandle = $message['ReceiptHandle'];
        $messageId = $message['MessageId'];
        $body = $message['Body'];
        $data = $this->serializer->deserialize($body, 'array', 'json');
        $notifications = $this->notificationFactoryBag->create($data);
        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $this->processNotification($notification, $messageId);
        }

        $this->sqsClient->delete($receiptHandle);
    }

    /**
     * @param Notification $notification
     * @param string       $messageId
     */
    protected function processNotification(Notification $notification, $messageId)
    {
        $messageLog = sprintf('Process notification %s %s', $messageId, $notification->getMessage());
        $this->output->writeln($messageLog);
        $notification->setQueueId($messageId);
        $errors = $this->validator->validate($notification);
        if (count($errors) === 0) {
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            $this->output->writeln('Notification persisted');
            $this->notificationDispatcher->dispatchPushMessages($notification);
            $this->output->writeln('Notification dispatched');
        } else {
            $errorString = (string)$errors;
            $this->output->writeln('Notification validation error' . $errorString);
        }
    }




}