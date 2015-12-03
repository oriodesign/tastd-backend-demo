<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Doctrine\Common\Util\Debug;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tastd\Bundle\CoreBundle\Controller\BaseServiceController;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\WishCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\WishDeletedEvent;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Manager\WishManager;
use Tastd\Bundle\CoreBundle\Repository\WishRepository;


/**
 * Class WishController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.wish_controller")
 */
class WishController extends BaseServiceController
{
    /** @var WishRepository */
    protected $wishRepository;
    /** @var WishManager */
    protected $wishManager;

    /**
     * @param WishRepository $wishRepository
     * @param WishManager    $wishManager
     */
    public function __construct(WishRepository $wishRepository, WishManager $wishManager)
    {
        $this->wishRepository = $wishRepository;
        $this->wishManager = $wishManager;
    }

    /**
     * @ApiDoc(
     *  description="Wish list",
     *  statusCodes={200="Wish list"},
     *  section="Wish",
     *  filters={
     *      {"name"="cuisine", "dataType"="integer", "required"=false},
     *      {"name"="geoname", "dataType"="integer", "required"=false},
     *      {"name"="user", "dataType"="integer", "required"=false},
     *      {"name"="restaurant", "dataType"="integer", "required"=false},
     *      {"name"="page", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/wishes")
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $pager = $this->wishRepository->getAllWishesPager($request);
        $this->cacheManager->tagController($request, CacheTag::WISH);

        return $this->view($this->getPagedViewData($pager, 'wishes'));
    }

    /**
     * @ApiDoc(
     *  description="New Wish",
     *  statusCodes={201="Wish"},
     *  section="Wish",
     *  parameters={
     *      {"name"="restaurant[id]", "dataType"="integer", "required"=true },
     *      {"name"="cuisine[id]", "dataType"="integer", "required"=true },
     *      {"name"="geoname[id]", "dataType"="integer", "required"=true }
     * })
     * @Route("/api/wishes")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $user = $this->getUser();
        /** @var Wish $wish */
        $wish = $this->deserializeCreateRequest(Wish::CLASS_NAME);
        $wish->setUser($user);
        $this->wishManager->deduceMissingFields($wish);
        $this->validate($wish);
        $user->incrementWishesCount();
        $this->entityManager->persist($wish);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::WISH_CREATED, new WishCreatedEvent($wish));
        $this->cacheManager->invalidateOnInsert($wish);

        return $this->view(array('wish' => $wish), 201);
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Delete Wish",
     *  statusCodes={204="Wish Deleted"},
     *  section="Wish")
     * @Route("/api/wishes/{id}")
     * @Method({"DELETE"})
     * @return View
     */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $wish = $this->wishRepository->get($id);
        $this->securityCheck(Permission::WRITE, $wish);
        $this->entityManager->remove($wish);
        $user->decrementWishesCount();
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::WISH_DELETED, new WishDeletedEvent($wish));
        $this->cacheManager->invalidateOnDelete($wish);

        return $this->view(array(), 204);
    }

}