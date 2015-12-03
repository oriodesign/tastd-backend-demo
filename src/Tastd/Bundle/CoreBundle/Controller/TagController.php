<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Tag;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Repository\TagRepository;


/**
 * Class TagController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.tag_controller")
 */
class TagController extends BaseServiceController
{
    /** @var TagRepository */
    protected $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @ApiDoc(
     *  description="Tags list",
     *  statusCodes={200="Tags"},
     *  section="Tag",
     *  filters={
     *     {"name"="name", "dataType"="string", "required"=false},
     *     {"name"="like", "dataType"="boolean", "required"=false},
     *     {"name"="highlight", "dataType"="boolean", "required"=false},
     *     {"name"="geoname", "dataType"="integer", "required"="false"},
     *     {"name"="users", "dataType"="integer", "required"="false"},
     *     {"name"="wish", "dataType"="boolean", "required"="false"},
     *     {"name"="withWish", "dataType"="boolean", "required"="false"},
     *     {"name"="groupId", "dataType"="boolean", "required"="false"},
     *     {"name"="leadersOf", "dataType"="integer", "required"="false"},
     *     {"name"="user", "dataType"="integer", "required"="false"}
     * })
     * @Route("/api/tags")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->fixRequest($request); // @TODO Remove this from next release
        $pager = $this->tagRepository->getAll($request);
        $pager->setMaxPerPage(200);
        $this->cacheManager->tagController($request, CacheTag::TAG);

        return $this->view($this->getPagedViewData($pager, 'tags'));
    }


    /**
     * @ApiDoc(
     *  description="New Tag",
     *  statusCodes={201="Tag"},
     *  section="Tag",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true },
     *      {"name"="groupId", "dataType"="string", "required"=true }
     *  })
     * @Route("/api/tags")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $user = $this->getUser();
        $tag = $this->deserializeCreateRequest(Tag::CLASS_NAME);
        $this->validate($tag);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnInsert($tag);

        return $this->view(array('tag' => $tag), 201);
    }

    /**
     * @param Request $request
     */
    protected function fixRequest(Request $request)
    {
        $highlightedOrInsertedBy = $request->query->get('highlightedOrInsertedBy');
        if ($highlightedOrInsertedBy === 'true') {
            $request->query->set('highlightedOrInsertedBy', $this->getUser()->getId());
            $highlightedOrInsertedBy = $request->query->get('highlightedOrInsertedBy');
        }

        if ($highlightedOrInsertedBy) {
            $request->query->set('user', $highlightedOrInsertedBy);
        }
    }

}