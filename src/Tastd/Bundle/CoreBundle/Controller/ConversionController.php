<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tastd\Bundle\CoreBundle\Entity\Conversion;
use Tastd\Bundle\CoreBundle\Entity\Invite;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\InviteCreatedEvent;
use Tastd\Bundle\CoreBundle\Repository\ConversionRepository;
use Tastd\Bundle\CoreBundle\Repository\InviteRepository;

/**
 * Class ConversionController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.conversion_controller")
 */
class ConversionController extends BaseServiceController
{
    /** @var ConversionRepository */
    protected $conversionRepository;
    /** @var InviteRepository */
    protected $inviteRepository;

    /**
     * @param ConversionRepository $conversionRepository
     * @param InviteRepository     $inviteRepository
     */
    public function __construct(ConversionRepository $conversionRepository, InviteRepository $inviteRepository)
    {
        $this->conversionRepository = $conversionRepository;
        $this->inviteRepository = $inviteRepository;
    }

    /**
     * @ApiDoc(
     *  description="Create a conversion",
     *  statusCodes={200="Success"},
     *  section="Conversion",
     *  parameters={
     *      {"name"="fingerprint", "dataType"="string", "required"=true},
     *      {"name"="inviteCode", "dataType"="string", "required"=true}
     *     }
     * )
     * @Route("/public-api/conversions")
     * @Template{}
     * @Method({"POST"})
     * @return mixed
     */
    public function newAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $invites = $this->inviteRepository->getByCode($request->request->get('inviteCode'));

        if (count($invites)!==1) {
            return $this->view(array('message' => 'Impossible find invite'));
        }

        $conversion = new Conversion();
        $conversion->setFingerprint($request->request->get('fingerprint'));
        $conversion->setInvite($invites[0]);
        $this->validate($conversion);
        $this->entityManager->persist($conversion);
        $this->entityManager->flush();

        return $this->view(array('conversion' => $conversion), 201);
    }
}
