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


/**
 * Class PhotoController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.photo_controller")
 */
class PhotoController extends BaseServiceController
{
    /** @var PhotoRepository */
    protected $photoRepository;
    /** @var S3Client */
    protected $s3Client;

    /**
     * @param PhotoRepository $photoRepository
     * @param S3Client $s3Client
     */
    public function __construct(
        PhotoRepository $photoRepository,
        S3Client $s3Client)
    {
        $this->photoRepository = $photoRepository;
        $this->s3Client = $s3Client;
    }

    /**
     * @ApiDoc(
     *  description="New Photo",
     *  statusCodes={201="Photo"},
     *  section="Photo",
     *  parameters={
     *      {"name"="uploadedPicture", "dataType"="string", "required"=false },
     *      {"name"="restaurant[id]", "dataType"="integer", "required"=true },
     *  })
     * @Route("/api/photos")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $user = $this->getUser();
        /** @var Photo $photo */
        $photo = $this->deserializeCreateRequest(Photo::CLASS_NAME);
        $photo->setUser($user);

        if ($photo->getUploadedPicture()) {
            $pictureFilename = $this->s3Client->uploadBase64($photo->getUploadedPicture(), 'photos/', 'jpg', 1080, 1080);
            $photo->setSrc($pictureFilename);
            $thumbFilename = $this->s3Client->uploadBase64($photo->getUploadedPicture(), 'photos_thumb/', 'jpg', 200, 200);
            $photo->setThumb($thumbFilename);
        }

        $this->validate($photo);
        $this->entityManager->persist($photo);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::PHOTO_CREATED, new PhotoCreatedEvent($photo, $this->getUser()));
        $this->cacheManager->invalidateOnInsert($photo);

        return $this->view(array('photo' =>$photo), 201);
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Delete Photo",
     *  statusCodes={204="Photo Deleted"},
     *  section="Photo")
     * @Route("/api/photos/{id}")
     * @Method({"DELETE"})
     * @return View
     */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $photo = $this->photoRepository->get($id);
        $this->securityCheck(Permission::WRITE, $photo);
        $this->entityManager->remove($photo);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::PHOTO_DELETED, new PhotoDeletedEvent($photo));
        $this->cacheManager->invalidateOnDelete($photo);

        return $this->view(array(), 204);
    }

}