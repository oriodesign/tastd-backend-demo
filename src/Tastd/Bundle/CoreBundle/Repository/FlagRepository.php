<?php

namespace Tastd\Bundle\CoreBundle\Repository;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Flag;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Entity\Wish;
use Tastd\Bundle\CoreBundle\Pager\OutOfRangePager;

/**
 * Class FlagRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class FlagRepository
{
    /** @var EntityManager $entityManager */
    protected $entityManager;


    /** @var ReviewRepository $reviewRepository */
    protected $reviewRepository;

    /** @var WishRepository $wishRepository */
    protected $wishRepository;

    /**
     * __construct
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->reviewRepository = $entityManager->getRepository(Review::SHORTCUT_CLASS_NAME);
        $this->wishRepository = $entityManager->getRepository(Wish::SHORTCUT_CLASS_NAME);
    }

    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getFlagsPager(Request $request)
    {
        $results = $this->getAll($request);
        $adapter = new ArrayAdapter($results);
        $pager = new Pagerfanta($adapter);
        $pageNumber = $request->query->get('page', 1);
        $pager->setMaxPerPage(40);
        try {
            $pager = $pager->setCurrentPage($pageNumber);
        } catch (\Exception $e) {
            $adapter = new ArrayAdapter(array());
            $pager = new OutOfRangePager($adapter);
            $pager->setOriginalPage($pageNumber);
        }

        return $pager;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAll(Request $request)
    {
        $wish = $request->query->get('wish') === 'true';
        $withWish = $request->query->get('withWish') === 'true';
        $restaurantsIds = $this->getRestaurantsIds($request);

        if ($wish) {
            $data = $this->wishRepository->getDataForFlags($request, $restaurantsIds);
            $flags = $this->dataToFlags($data, false);
        } else {
            $data = $this->reviewRepository->getDataForFlags($request, $restaurantsIds);
            $flags = $this->dataToFlags($data, true);
        }

        if ($withWish) {
            $data = $this->wishRepository->getDataForFlags($request, $restaurantsIds);
            $wishFlags = $this->dataToFlags($data, false);
            $flags = $this->mergeFlagsAndWishes($wishFlags, $flags);
        }

        $this->sortFlags($flags, $request);

        return $flags;
    }

    protected function getRestaurantsIds(Request $request) {
        $reviewedBy = $request->query->get('reviewedBy');
        $wishedBy = $request->query->get('wishedBy');
        $results = array();

        if ($reviewedBy && $wishedBy) {
            $wishedRestaurants = $this->wishRepository->getWishesIdsOf($wishedBy);
            $reviewedRestaurants = $this->reviewRepository->getReviewsIdsOf($reviewedBy);
            $results = array_unique(array_merge($wishedRestaurants, $reviewedRestaurants));

        } else if ($reviewedBy) {
            $results = $this->reviewRepository->getReviewsIdsOf($reviewedBy);
        } else if ($wishedBy) {
            $results = $this->wishRepository->getWishesIdsOf($wishedBy);
        }

        return $results;
    }

    /**
     * @param array $wishFlags
     * @param array $flags
     *
     * @return array
     */
    protected function mergeFlagsAndWishes($wishFlags, $flags)
    {
        $flags = array_merge($flags, $wishFlags);
        $restaurants = array();
        $results = array();
        /** @var Flag $flag */
        foreach ($flags as $key => $flag) {
            if (!in_array($flag->getRestaurantId(), $restaurants)) {
                $restaurants[] = $flag->getRestaurantId();
                $results[] = $flag;
            }
        }
        return $results;
    }

    /**
     * @param $flags
     * @param Request $request
     */
    protected function sortFlags(&$flags, Request $request)
    {
        $orderBy = $request->query->get('orderBy');

        if ($orderBy === 'cuisine') {
            $this->sortFlagsByCuisine($flags);
        } else {
            $this->sortFlagsByPosition($flags);
        }
    }

    /**
     * @param array $flags
     */
    protected function sortFlagsByPosition(&$flags)
    {
        usort($flags, function(Flag $a, Flag $b){
            if ($a->getPosition() === $b->getPosition()) {
                return strcasecmp($a->getName(), $b->getName()) ? 1 : -1;
            }
            return  $a->getPosition() > $b->getPosition() ? 1 : -1;
        });
    }

    /**
     * @param array $flags
     */
    protected function sortFlagsByCuisine(&$flags)
    {
        usort($flags, function(Flag $a, Flag $b){
            if ($a->getCuisineName() === $b->getCuisineName()) {
                if ($a->getPosition() === $b->getPosition()) {
                    return strcasecmp($a->getName(), $b->getName()) ? 1 : -1;
                }
                if (null === $a->getPosition()) {
                    return 1;
                }
                if (null === $b->getPosition()) {
                    return -1;
                }

                return  $a->getPosition() > $b->getPosition() ? 1 : -1;
            }
            return  $a->getCuisineName() > $b->getCuisineName() ? 1 : -1;
        });
    }

    /**
     * @param array   $data
     * @param boolean $withPosition
     *
     * @return array
     */
    protected function dataToFlags($data, $withPosition)
    {
        $flags = array();
        $positions = array();
        foreach ($data as $reviewData) {
            $cuisineName = $reviewData['cuisineName'];
            $flag = new Flag();
            $flag->setLat($reviewData['lat']);
            $flag->setLng($reviewData['lng']);
            $flag->setName($reviewData['name']);
            $flag->setColor($reviewData['color']);
            $flag->setPicture($reviewData['picture']);
            $flag->setCuisineName($reviewData['cuisineName']);
            $flag->setFormattedAddress($reviewData['address']);
            $flag->setRestaurantId($reviewData['restaurantId']);
            $flag->setCuisineId($reviewData['cuisineId']);
            if ($withPosition) {
                $positions[$cuisineName] = isset($positions[$cuisineName]) ? ++$positions[$cuisineName] : 1;
                $flag->setPosition($positions[$cuisineName]);
            }
            $flags[] = $flag;
        }

        return $flags;
    }

}