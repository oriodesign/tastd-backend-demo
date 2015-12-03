<?php

namespace Tastd\Bundle\CoreBundle\Listener;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Event\ReviewCreatedEvent;
use Tastd\Bundle\CoreBundle\Repository\CuisineRepository;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\WishRepository;

/**
 * Class CreateReviewListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class CreateReviewListener
{
    protected $reviewRepository;
    protected $entityManager;
    protected $cuisineRepository;
    protected $wishRepository;

    /**
     * @param EntityManager     $entityManager
     * @param ReviewRepository  $reviewRepository
     * @param CuisineRepository $cuisineRepository
     * @param WishRepository    $wishRepository
     */
    public function __construct(
        EntityManager $entityManager,
        ReviewRepository $reviewRepository,
        CuisineRepository $cuisineRepository,
        WishRepository $wishRepository)
    {
        $this->entityManager = $entityManager;
        $this->reviewRepository = $reviewRepository;
        $this->cuisineRepository = $cuisineRepository;
        $this->wishRepository = $wishRepository;
    }

    /**
     * @param ReviewCreatedEvent $reviewCreatedEvent
     */
    public function onReviewCreated(ReviewCreatedEvent $reviewCreatedEvent)
    {
        $review = $reviewCreatedEvent->getReview();
        $this->updateRestaurantAverageData($review);
        $this->deleteWishForSameRestaurant($review);

        $this->entityManager->flush();
    }

    /**
     * @param Review $review
     */
    private function deleteWishForSameRestaurant(Review $review)
    {
        $wishes = $this->wishRepository->getAllByUserAndRestaurant($review->getUser(), $review->getRestaurant());
        foreach ($wishes as $wish) {
            $this->entityManager->remove($wish);
        }
    }

    /**
     * @param Review $review
     */
    private function updateRestaurantAverageData(Review $review)
    {
        $restaurant = $review->getRestaurant();
        $averageCost = $this->reviewRepository->getAverageCost($review->getRestaurant());
        $cuisine = $this->cuisineRepository->getMostUsedCuisine($review->getRestaurant());
        $restaurant->setAverageCost($averageCost);
        $restaurant->setCuisine($cuisine);
    }


}