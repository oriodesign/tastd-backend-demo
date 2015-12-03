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
use Tastd\Bundle\CoreBundle\Aws\S3Client;
use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\PhotoCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\PhotoDeletedEvent;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Repository\PhotoRepository;
use Tastd\Bundle\CoreBundle\Repository\PostRepository;


/**
 * Class PostController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.post_controller")
 */
class PostController extends BaseServiceController
{
    /** @var PostRepository */
    protected $postRepository;

    /**
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @ApiDoc(
     *  description="Posts list",
     *  statusCodes={200="Posts"},
     *  section="Post")
     * @Route("/public-api/posts")
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $posts = $this->postRepository->getAll();

        return $this->view(array('posts'=> $posts));
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Post detail",
     *  statusCodes={200="Post"},
     *  section="Post")
     * @Route("/public-api/posts/{id}")
     * @Method({"GET"})
     * @return View
     */
    public function getAction($id)
    {
        $post = $this->postRepository->get($id);

        return $this->view(array('post' => $post));
    }
}