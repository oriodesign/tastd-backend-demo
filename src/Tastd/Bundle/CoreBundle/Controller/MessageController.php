<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tastd\Bundle\CoreBundle\Entity\Message;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\MessageCreatedEvent;
use Tastd\Bundle\CoreBundle\Repository\MessageRepository;

/**
 * Class MessageController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.message_controller")
 */
class MessageController extends BaseServiceController
{
    /** @var MessageRepository  */
    protected $messageRepository;

    /**
     * @param MessageRepository $messageRepository
     */
    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @ApiDoc(
     *  description="Get all messages",
     *  statusCodes={200="Success"},
     *  section="Message",
     *  filters={
     *      {"name"="title", "dataType"="string", "required"=true},
     *      {"name"="content", "dataType"="string", "required"=true},
     *      {"name"="category", "dataType"="string", "required"=true, "pattern"="INFO|BUG|RESTAURANT"}
     *  }
     * )
     * @Route("/api/messages")
     * @Template{}
     * @Method({"POST"})
     * @return mixed
     */
    public function newAction()
    {
        $user = $this->getUser();
        /** @var Message $message */
        $message = $this->deserializeCreateRequest(Message::CLASS_NAME);
        $message->setUser($user);
        $this->validate($message);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::MESSAGE_CREATED, new MessageCreatedEvent($message));

        return $this->view(array('message' => $message), 201);
    }

}
