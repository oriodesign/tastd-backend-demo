<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tastd\Bundle\CoreBundle\Entity\Invite;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\InviteCreatedEvent;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\CredentialNotFoundException;
use Tastd\Bundle\CoreBundle\Facebook\FacebookClient;
use Tastd\Bundle\CoreBundle\Key\CredentialProvider;
use Tastd\Bundle\CoreBundle\Key\InviteChannel;

/**
 * Class InviteController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.invite_controller")
 */
class InviteController extends BaseServiceController
{
    /**
     * @var FacebookClient $facebookClient
     */
    protected $facebookClient;

    /**
     * @param FacebookClient $facebookClient
     */
    public function __construct(FacebookClient $facebookClient)
    {
        $this->facebookClient = $facebookClient;
    }

    /**
     * @ApiDoc(
     *  description="Create an invite",
     *  statusCodes={200="Success"},
     *  section="Invite",
     *  parameters={
     *      {"name"="channel", "dataType"="string", "required"=true, "description"="EMAIL"},
     *      {"name"="recipients", "dataType"="string", "required"=false, "description"="aaa@email.com,bbb@email.com"}
     *     }
     * )
     * @Route("/api/invites")
     * @Template{}
     * @Method({"POST"})
     * @return mixed
     */
    public function newAction()
    {
        $user = $this->getUser();
        /** @var Invite $invite */
        $invite = $this->deserializeCreateRequest(Invite::CLASS_NAME);
        $invite->setUser($user);
        $this->validate($invite);
        $this->entityManager->persist($invite);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::INVITE_CREATED, new InviteCreatedEvent($invite));

        return $this->view(array('invite' => $invite), 201);
    }

    /**
     * @ApiDoc(
     *  description="Post a public invite on facebook wall",
     *  statusCodes={200="Success"},
     *  section="Invite",
     *  parameters={
     *      {"name"="message", "dataType"="string", "required"=true}
     *     }
     * )
     * @Route("/api/facebook-public-invite")
     * @Template{}
     * @Method({"POST"})
     * @return mixed
     * @throws CredentialNotFoundException
     */
    public function facebookPublicInviteAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $message = $request->request->get('message');
        $user = $this->getUser();
        $credential = $user->getCredentialByProvider(CredentialProvider::FACEBOOK);
        if (!$credential) {
            throw new CredentialNotFoundException();
        }
        $this->facebookClient->connect($credential->getToken());
        $invite = new Invite();
        $invite->setChannel(InviteChannel::FACEBOOK);
        $invite->setUser($user);
        $this->entityManager->persist($invite);
        $this->entityManager->flush();
        $this->facebookClient->publishLink('https://tastdapp.com/invite?invite=' . $invite->getCode(), $message);

        return $this->view(array('invite' => $invite), 201);
    }


}
