<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Tastd\Bundle\CoreBundle\Entity\Option;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\AccessDeniedException;
use Tastd\Bundle\CoreBundle\Exception\Api\Validation\AssociationException;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Repository\OptionRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class OptionController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.option_controller")
 */
class OptionController extends BaseServiceController
{
    /** @var OptionRepository */
    protected $optionRepository;
    /** @var UserRepository */
    protected $userRepository;

    /**
     * @param OptionRepository $optionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        OptionRepository $optionRepository,
        UserRepository $userRepository)
    {
        $this->optionRepository = $optionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @ApiDoc(
     *  description="Option list",
     *  statusCodes={200="Option"},
     *  section="Option",
     *  filters={
     *      {"name"="user", "dataType"="string", "required"=true}
     *  })
     * @Route("/api/options")
     * @throws AccessDeniedException
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $me = $this->getUser();
        $user = $this->userRepository->get($request->query->get('user'));
        if ($user->getId() !== $me->getId()) {
            throw new AccessDeniedException();
        }
        $options = $this->optionRepository->getAllByUser($user);
        $this->cacheManager->tagController($request, CacheTag::OPTION);

        return $this->view(array('options'=>$options));
    }

    /**
     * @ApiDoc(
     *  description="Create Option",
     *  statusCodes={201="Option Created"},
     *  section="Option",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true},
     *      {"name"="content", "dataType"="string", "required"=true}
     *  })
     * @Route("/api/options")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $option = $this->deserializeCreateRequest(Option::CLASS_NAME);
        $user = $this->getUser();
        $option->setUser($user);
        $this->validate($option);
        $this->entityManager->persist($option);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnInsert($option);

        return $this->view(array('option' => $option), 201);
    }

    /**
     * @ApiDoc(
     *  description="Patch Option",
     *  statusCodes={200="Option Updated",404="User Not Found"},
     *  section="Option",
     *  parameters={
     *      {"name"="content", "dataType"="string", "required"=true},
     *      {"name"="seen", "dataType"="string", "required"=true} 
     *  })
     * @Route("/api/options/{id}")
     * @Method({"PUT"})
     * @return View
     * @throws AssociationException
     */
    public function putAction()
    {
        $option = $this->deserializeUpdateRequest(Option::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $option);
        $this->validate($option);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnUpdate($option);

        return $this->view(array('option' => $option));
    }

}