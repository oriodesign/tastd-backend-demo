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
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Controller\BaseServiceController;
use Tastd\Bundle\CoreBundle\Entity\Ranking;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Event\ReviewCreatedEvent;
use Tastd\Bundle\CoreBundle\Event\ReviewDeletedEvent;
use Tastd\Bundle\CoreBundle\Event\ReviewUpdatedEvent;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Key\SerializationGroup;
use Tastd\Bundle\CoreBundle\Manager\ReviewManager;
use Tastd\Bundle\CoreBundle\Repository\RestaurantRepository;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;


/**
 * Class ReviewController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.review_controller")
 */
class ReviewController extends BaseServiceController
{
    /** @var ReviewRepository */
    protected $reviewRepository;
    /** @var RestaurantRepository */
    protected $restaurantRepository;
    /** @var ReviewManager */
    protected $reviewManager;

    /**
     * @param ReviewRepository $reviewRepository
     * @param RestaurantRepository $restaurantRepository
     * @param ReviewManager $reviewManager
     */
    public function __construct(
        ReviewRepository $reviewRepository,
        RestaurantRepository $restaurantRepository,
        ReviewManager $reviewManager)
    {
        $this->reviewRepository = $reviewRepository;
        $this->restaurantRepository = $restaurantRepository;
        $this->reviewManager = $reviewManager;
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Review detail",
     *  statusCodes={200="Review"},
     *  section="Review")
     * @Route("/api/reviews/{id}")
     * @Route("/public-api/reviews/{id}")
     * @Method({"GET"})
     * @Cache(maxage="+1 week", public=true)
     * @return View
     */
    public function getAction($id)
    {
        $review = $this->reviewRepository->get($id);
        $this->reviewManager->hydrateReviewPhotos($review);
        $serializationsGroups = array(SerializationGroup::BASE, SerializationGroup::MIN, SerializationGroup::REVIEW_PHOTOS);

        return $this->view(array('review' => $review), 200, array(), $serializationsGroups);
    }

    /**
     * @ApiDoc(
     *  description="Review list",
     *  statusCodes={200="Review"},
     *  section="Review",
     *  filters={
     *      {"name"="cuisine", "dataType"="integer", "required"=false},
     *      {"name"="geoname", "dataType"="integer", "required"=false},
     *      {"name"="user", "dataType"="integer", "required"=false},
     *      {"name"="leadersOf", "dataType"="integer", "required"=false},
     *      {"name"="users", "dataType"="integer", "required"=false},
     *      {"name"="restaurant", "dataType"="integer", "required"=false},
     *      {"name"="minCost", "dataType"="integer", "required"=false},
     *      {"name"="maxCost", "dataType"="integer", "required"=false},
     *      {"name"="orderBy", "dataType"="string", "required"=false, "pattern"="position|created"},
     *      {"name"="tags", "dataType"="string", "required"=false},
     *      {"name"="page", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/reviews")
     * @Route("/public-api/reviews")
     * @Cache(maxage="+1 week", public=true)
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $request = $this->fixRequest($request);
        $pager = $this->reviewRepository->getAllReviewsPager($request);
        $this->reviewManager->hydrateReviewsPhotos($pager->getCurrentPageResults());
        $serializationsGroups = array(SerializationGroup::BASE, SerializationGroup::MIN, SerializationGroup::REVIEW_PHOTOS);
        $this->cacheManager->tagController($request, CacheTag::REVIEW);

        return $this->view($this->getPagedViewData($pager, 'reviews'), 200, array(), $serializationsGroups);
    }

    /**
     * HotFix v1.4 - Wrong parameter name
     *
     * @param Request $request
     * @return Request
     */
    public function fixRequest(Request $request)
    {
        $cityId = $request->query->get('cityId');
        if ($cityId) {
            $request->query->set('geoname', $cityId);
        }

        return $request;
    }


    /**
     * @ApiDoc(
     *  description="New Review",
     *  statusCodes={201="Review"},
     *  section="Review",
     *  parameters={
     *      {"name"="cost", "dataType"="integer", "required"=true },
     *      {"name"="position", "dataType"="integer", "required"=true },
     *      {"name"="restaurant[id]", "dataType"="integer", "required"=true },
     *      {"name"="cuisine[id]", "dataType"="integer", "required"=true },
     *      {"name"="comment", "dataType"="string", "required"=false },
     *      {"name"="geoname[id]", "dataType"="integer", "required"=true },
     *      {"name"="drinkCost", "dataType"="integer", "required"=false },
     *      {"name"="lastVisited", "dataType"="string", "required"=false },
     *      {"name"="visitCount", "dataType"="integer", "required"=false },
     *      {"name"="mustHave", "dataType"="string", "required"=false },
     *      {"name"="place", "dataType"="string", "required"=false },
     *      {"name"="dressCode", "dataType"="string", "required"=false },
     *      {"name"="discoveredOn", "dataType"="string", "required"=false },
     *      {"name"="dishes", "dataType"="string", "required"=false }
     * })
     * @Route("/api/reviews")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $user = $this->getUser();
        /** @var Review $review */
        $review = $this->deserializeCreateRequest(Review::CLASS_NAME);
        $review->setUser($user);
        $this->reviewManager->deduceMissingFields($review);
        $this->validate($review);
        $this->entityManager->persist($review);
        $user->incrementReviewsCount();
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::REVIEW_CREATED, new ReviewCreatedEvent($review));
        $this->cacheManager->invalidateOnInsert($review);

        return $this->view(array('review' =>$review), 201);
    }

    /**
     * @param int $id
     *
     * @ApiDoc(
     *  description="Update Review",
     *  statusCodes={200="Review"},
     *  section="Review",
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true },
     *      {"name"="cost", "dataType"="integer", "required"=true },
     *      {"name"="comment", "dataType"="integer", "required"=true }
     *  })
     * @Route("/api/reviews/{id}")
     * @Method({"PUT"})
     * @return View
     */
    public function updateAction($id)
    {
        $oldReview = $this->reviewRepository->get($id);
        $review = $this->deserializeUpdateRequest(Review::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $review);
        $this->validate($review);
        $this->entityManager->flush();
        $this->dispatch(ApiEvent::REVIEW_UPDATED, new ReviewUpdatedEvent($oldReview, $review));
        $this->cacheManager->invalidateOnUpdate($review);

        return $this->view(array('review' => $review));
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Delete Review",
     *  statusCodes={204="Review Deleted"},
     *  section="Review")
     * @Route("/api/reviews/{id}")
     * @Method({"DELETE"})
     * @return View
     */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $review = $this->reviewRepository->get($id);
        $this->securityCheck(Permission::WRITE, $review);
        $this->entityManager->remove($review);
        $user->decrementReviewsCount();
        $this->entityManager->flush();
        $this->reviewManager->autoReorderRankingOfReview($review);
        $this->dispatch(ApiEvent::REVIEW_DELETED, new ReviewDeletedEvent($review));
        $this->cacheManager->invalidateOnDelete($review);

        return $this->view(array(), 204);
    }

    /**
     * @ApiDoc(
     *  description="Reorder Reviews",
     *  statusCodes={200="Review Reordered"},
     *  section="Review",
     *  parameters={
     *      {"name"="reviews[1][id]", "dataType"="integer", "required"=true },
     *      {"name"="reviews[1][position]", "dataType"="integer", "required"=true },
     *      {"name"="reviews[2][id]", "dataType"="integer", "required"=true },
     *      {"name"="reviews[2][position]", "dataType"="integer", "required"=true }
     *  }))
     * @Route("/api/reviews/reorder")
     * @Method({"POST"})
     * @return View
     */
    public function reorderAction()
    {
        /** @var Ranking $ranking */
        $ranking = $this->deserializeRequest(Ranking::CLASS_NAME, array(SerializationGroup::REORDER), 'json');
        foreach ($ranking->getReviews() as $review) {
            /** @var Review $review */
            $this->securityCheck(Permission::WRITE, $review);
            $this->cacheManager->invalidateOnUpdate($review);
        }
        $this->entityManager->flush();

        return $this->view(array('reviews'=> $ranking->getReviews()), 200);
    }

    /**
     * @ApiDoc(
     *  description="New Batch of Reviews",
     *  statusCodes={201="Batch of Reviews"},
     *  section="Review",
     *  parameters={
     *      {"name"="restaurants", "dataType"="integer", "required"=true }
     * })
     * @Route("/api/reviews/batch")
     * @Method({"POST"})
     * @return View
     */
    public function batchInsertAction()
    {
        $user = $this->getUser();
        $positions = array();
        $request = $this->requestStack->getCurrentRequest();
        $ids = $request->request->get('restaurants');
        $restaurants = $this->restaurantRepository->getByIds($ids);
        $reviews = array();
        /** @var Restaurant $restaurant */
        foreach ($restaurants as $restaurant) {
            if (null === $restaurant->getGeoname() ||
                null === $restaurant->getCuisine()) {
                continue;
            }

            if (isset($positions[$restaurant->getRankingKey()])) {
                $positions[$restaurant->getRankingKey()]++;
            } else {
                $positions[$restaurant->getRankingKey()] = 1;
            }

            $review = new Review();
            $review->setGeoname($restaurant->getGeoname());
            $review->setCost($restaurant->getAverageCost());
            $review->setCuisine($restaurant->getCuisine());
            $review->setPosition($positions[$restaurant->getRankingKey()]);
            $review->setUser($user);
            $review->setComment('');
            $review->setRestaurant($restaurant);
            $reviews[] = $review;
            $this->validate($review);
            $user->incrementReviewsCount();
            $this->entityManager->persist($review);
            $this->cacheManager->invalidateOnInsert($review);
        }
        $this->entityManager->flush();

        return $this->view(array('reviews' => $reviews), 201);
    }
}