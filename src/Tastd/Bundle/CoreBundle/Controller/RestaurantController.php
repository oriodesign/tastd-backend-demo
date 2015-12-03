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
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Tastd\Bundle\CoreBundle\Aws\S3Client;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\RestaurantCreatedEvent;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Key\SerializationGroup;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\PhotoRepository;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\TagRepository;
use Tastd\Bundle\CoreBundle\Repository\WishRepository;


/**
 * Class RestaurantController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.restaurant_controller")
 */
class RestaurantController extends BaseServiceController
{
    /** @var RestaurantRepository */
    protected $restaurantRepository;
    /** @var ReviewRepository */
    protected $reviewRepository;
    /** @var WishRepository */
    protected $wishRepository;
    /** @var TagRepository */
    protected $tagRepository;
    /** @var S3Client */
    protected $s3Client;
    /** @var CuisineRepository */
    protected $cuisineRepository;

    /**
     * @param RestaurantRepository $restaurantRepository
     * @param ReviewRepository     $reviewRepository
     * @param WishRepository       $wishRepository
     * @param TagRepository        $tagRepository
     * @param PhotoRepository      $photoRepository
     * @param CuisineRepository    $cuisineRepository
     * @param S3Client $s3Client
     */
    public function __construct(
        RestaurantRepository $restaurantRepository,
        ReviewRepository $reviewRepository,
        WishRepository $wishRepository,
        TagRepository $tagRepository,
        PhotoRepository $photoRepository,
        CuisineRepository $cuisineRepository,
        S3Client $s3Client)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->reviewRepository = $reviewRepository;
        $this->wishRepository = $wishRepository;
        $this->tagRepository = $tagRepository;
        $this->photoRepository = $photoRepository;
        $this->cuisineRepository = $cuisineRepository;
        $this->s3Client = $s3Client;
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Restaurant detail",
     *  statusCodes={200="Restaurant"},
     *  section="Restaurant")
     * @Route("/api/restaurants/{id}")
     * @Method({"GET"})
     * @return View
     */
    public function getAction($id)
    {
        $request = $this->requestStack->getCurrentRequest();
        $restaurant = $this->restaurantRepository->get($id);
        $data = array(
            'restaurant' => $restaurant
        );
        $this->cacheManager->tagController($request, CacheTag::RESTAURANT, false);

        return $this->view($data, 200, array(), array(SerializationGroup::RESTAURANT));
    }

    /**
     * @ApiDoc(
     *  description="Restaurant list",
     *  statusCodes={200="Restaurant"},
     *  section="Restaurant",
     *  filters={
     *      {"name"="name", "dataType"="string", "required"=true},
     *      {"name"="cuisine", "dataType"="string", "required"=false, "pattern"="not_null"},
     *      {"name"="averageCost", "dataType"="string", "required"=false, "pattern"="not_null"},
     *      {"name"="geoname", "dataType"="integer", "required"=false},
     *      {"name"="lat", "dataType"="string", "required"=false},
     *      {"name"="lng", "dataType"="string", "required"=false},
     *      {"name"="maxDistance", "dataType"="integer", "required"=false},
     *      {"name"="orderBy", "dataType"="string", "required"="false", "pattern"="score|distance"},
     *      {"name"="page", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/restaurants")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $pager = $this->restaurantRepository->getRestaurantsPager($request);
        $this->cacheManager->tagController($request, CacheTag::RESTAURANT);

        return $this->view($this->getPagedViewData($pager, 'restaurants'));
    }

    /**
     * @ApiDoc(
     *  description="New Restaurant",
     *  statusCodes={201="Restaurant"},
     *  section="Restaurant",
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true },
     *      {"name"="website", "dataType"="string", "required"=false },
     *      {"name"="chef", "dataType"="string", "required"=false },
     *      {"name"="instagram", "dataType"="string", "required"=false },
     *      {"name"="awards", "dataType"="string", "required"=false },
     *      {"name"="phone", "dataType"="string", "required"=false },
     *      {"name"="uploadedPicture", "dataType"="string", "required"=false },
     *      {"name"="geoname[id]", "dataType"="integer", "required"=true },
     *      {"name"="formatted_address", "dataType"="string", "required"=true },
     *      {"name"="lat", "dataType"="string", "required"=true },
     *      {"name"="lng", "dataType"="string", "required"=true }
     *  })
     * @Route("/api/restaurants")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $restaurant = $this->deserializeCreateRequest(Restaurant::CLASS_NAME);

        // @TODO move this to restaurant manager
        if ($restaurant->getUploadedPicture()) {
            $pictureFilename = $this->s3Client->uploadBase64($restaurant->getUploadedPicture(), 'restaurant/', 'jpg', 640, 380);
            $restaurant->setPicture($pictureFilename);
            $thumbFilename = $this->s3Client->uploadBase64($restaurant->getUploadedPicture(), 'restaurant_thumb/', 'jpg', 200, 200);
            $restaurant->setThumb($thumbFilename);
        } else {
            $restaurant->setPicture('restaurant/default0.jpg');
            $restaurant->setThumb('restaurant_thumb/default0.jpg');
        }

        $this->validate($restaurant);
        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::RESTAURANT_CREATED, new RestaurantCreatedEvent($restaurant, $this->getUser()));
        $this->cacheManager->invalidateOnInsert($restaurant);

        return $this->view(array('restaurant' =>$restaurant), 201);
    }

    /**
     * @ApiDoc(
     *  description="Update Restaurant",
     *  statusCodes={200="Restaurant"},
     *  section="Restaurant")
     * @Route("/api/restaurants/{id}")
     * @Method({"PUT"})
     * @return View
     */
    public function updateAction()
    {
        $restaurant = $this->deserializeUpdateRequest(Restaurant::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $restaurant);
        // @TODO move this to restaurant manager
        if ($restaurant->getUploadedPicture()) {
            $pictureFilename = $this->s3Client->uploadBase64($restaurant->getUploadedPicture(), 'restaurant/', 'jpg', 640, 380);
            $restaurant->setPicture($pictureFilename);
            $thumbFilename = $this->s3Client->uploadBase64($restaurant->getUploadedPicture(), 'restaurant_thumb/', 'jpg', 200, 200);
            $restaurant->setThumb($thumbFilename);
        }

        $this->validate($restaurant);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnUpdate($restaurant);

        return $this->view(array('restaurant' => $restaurant));
    }


    /**
     * @ApiDoc(
     *  description="Restaurant reviewed array of id",
     *  statusCodes={200="Restaurant"},
     *  section="Restaurant",
     *  filters={
     *      {"name"="user", "dataType"="integer", "required"=true}
     *  })
     * @Route("/api/restaurants/reviewed/ids")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @throws BadRequestException
     * @return View
     */
    public function getReviewedAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $userId = $request->query->get('user');
        if (!$userId) {
            throw new BadRequestException();
        }
        $restaurants = $this->reviewRepository->getReviewsIdsOf($userId);
        $this->cacheManager->tagController($request, CacheTag::REVIEW);

        return $this->view(array('restaurants' => $restaurants));
    }

    /**
     * @ApiDoc(
     *  description="Restaurant wished array of id",
     *  statusCodes={200="Restaurant"},
     *  section="Restaurant",
     *  filters={
     *      {"name"="user", "dataType"="integer", "required"=true}
     *  })
     * @Route("/api/restaurants/wished/ids")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @throws BadRequestException
     * @return View
     */
    public function getWishedAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $userId = $request->query->get('user');
        if (!$userId) {
            throw new BadRequestException();
        }
        $restaurants = $this->wishRepository->getWishesIdsOf($userId);
        $this->cacheManager->tagController($request, CacheTag::WISH);

        return $this->view(array('restaurants' => $restaurants));
    }


}