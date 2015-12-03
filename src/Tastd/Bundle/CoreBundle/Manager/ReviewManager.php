<?php

namespace Tastd\Bundle\CoreBundle\Manager;
use Doctrine\ORM\EntityManager;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Repository\PhotoRepository;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;

/**
 * Class ReviewManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class ReviewManager
{

    protected $reviewRepository;
    protected $photoRepository;
    protected $entityManager;

    /**
     * @param ReviewRepository $reviewRepository
     * @param PhotoRepository  $photoRepository
     * @param EntityManager    $entityManager
     */
    public function __construct(
        ReviewRepository $reviewRepository,
        PhotoRepository $photoRepository,
        EntityManager $entityManager)
    {
        $this->photoRepository = $photoRepository;
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Review $review
     */
    public function hydrateReviewPhotos(Review $review)
    {
        $photos = $this->photoRepository->getAllByUserAndRestaurant($review->getUser(), $review->getRestaurant());
        $review->setPhotos($photos);
    }

    /**
     * @param $reviews
     */
    public function hydrateReviewsPhotos($reviews)
    {
        // @TODO do all in a single query
        foreach ($reviews as $review) {
            $this->hydrateReviewPhotos($review);
        }
    }

    /**
     * @param Review $review
     */
    public function deduceMissingFields(Review $review)
    {
        $restaurant = $review->getRestaurant();
        $user = $review->getUser();

        if (null === $user || null === $restaurant) {
            return;
        }

        if (null === $review->getGeoname()) {
            $review->setGeoname($restaurant->getGeoname());
        }

        if (null === $review->getCuisine()) {
            $review->setCuisine($restaurant->getCuisine());
        }

        if (null === $review->getCost()) {
            $review->setCost($restaurant->getAverageCost());
        }

        if (null === $review->getPosition()) {
            $max = $this->reviewRepository->getMaxPosition($user, $review);
            $review->setPosition($max + 1);
        }
    }

    /**
     * @param Review $review
     */
    public function reorderReviewsWithHigherPosition(Review $review)
    {
        $this->reviewRepository->decrementReviewsWithHigherPosition($review);
    }


    /**
     * Update review position based on real order after ORDER BY
     *
     * @param int $userId
     * @param int $geonameId
     * @param int $cuisineId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function autoReorderRanking($userId, $geonameId, $cuisineId)
    {
        $sql =
            'UPDATE reviews INNER JOIN (
                SELECT @rownum := @rownum + 1 AS rank, id
                FROM reviews, (SELECT @rownum := 0) r
                WHERE user_id = ?
                AND geoname_id = ?
                AND cuisine_id = ?
                ORDER BY position ASC
                ) selection
            ON reviews.id = selection.id
            SET reviews.position = selection.rank';

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->bindValue(2, $geonameId);
        $stmt->bindValue(3, $cuisineId);
        $stmt->execute();
    }

    /**
     * @param Review $review
     */
    public function autoReorderRankingOfReview(Review $review)
    {
        $this->autoReorderRanking(
            $review->getUser()->getId(),
            $review->getGeoname()->getId(),
            $review->getCuisine()->getId()
        );
    }

}