<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\CredentialNotFoundException;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Facebook\FacebookClient;
use Tastd\Bundle\CoreBundle\Key\CredentialProvider;
use Tastd\Bundle\CoreBundle\Key\GeocodePrecision;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class FriendsController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.friends_controller")
 */
class FriendsController extends BaseServiceController
{
    /** @var FacebookClient */
    protected $facebookClient;
    /** @var UserRepository */
    protected $userRepository;

    /**
     * @param FacebookClient $facebookClient
     * @param UserRepository $userRepository
     */
    public function __construct(FacebookClient $facebookClient, UserRepository $userRepository)
    {
        $this->facebookClient = $facebookClient;
        $this->userRepository = $userRepository;
    }

    /**
     * @ApiDoc(
     *  description="Get friends from facebook already subscribed in Tastd",
     *  statusCodes={200="Get My friends"},
     *  section="Friends")
     * @Route("/api/friends")
     * @Method({"GET"})
     * @throws CredentialNotFoundException
     * @return View
     */
    public function getAction()
    {
        $user = $this->getUser();
        $facebookCredential = $user->getCredentialByProvider(CredentialProvider::FACEBOOK);
        if (!$facebookCredential) {
            throw new CredentialNotFoundException();
        }
        $this->facebookClient->connect($facebookCredential->getToken());
        $ids = $this->facebookClient->getFriendsIds();
        $friends = $this->userRepository->getUsersByCredentialExternalIds(CredentialProvider::FACEBOOK, $ids);

        return $this->view(array('friends' => $friends, 'ids'=> $ids));
    }
}