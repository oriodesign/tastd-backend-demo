<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Photo;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\PhotoNotFoundException;

/**
 * Class RestaurantRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class PhotoRepository extends BaseEntityRepository
{
    /**
     * @param integer $id
     *
     * @return Photo
     * @throws PhotoNotFoundException
     */
    public function get($id)
    {
        $restaurant = $this->find($id);
        if (!$restaurant) {
            throw new PhotoNotFoundException();
        }

        return $restaurant;
    }


    /**
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getAll($request)
    {
        $page = $request->query->get('page', 1);
        $restaurant = $request->query->get('restaurant');
        $user = $request->query->get('user');
        $users = $request->query->get('users');

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Photo::SHORTCUT_CLASS_NAME, 'p');

        if (isset($restaurant)) {
            $queryBuilder
                ->andWhere('p.restaurant = :restaurant')
                ->setParameter('restaurant', $restaurant);
        }

        if (isset($user)) {
            $queryBuilder
                ->andWhere('p.user = :user')
                ->setParameter('user', $user);
        }

        if (isset($users)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('u.id', ':users'))
                ->setParameter('users', explode(',', $users));
        }

        return $this->getPager($queryBuilder, $page);
    }

    /**
     * @param User       $user
     * @param Restaurant $restaurant
     *
     * @return array
     */
    public function getAllByUserAndRestaurant(User $user, Restaurant $restaurant)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Photo::SHORTCUT_CLASS_NAME, 'p')
            ->where('p.user = :user')
            ->andWhere('p.restaurant = :restaurant')
            ->setParameter('user', $user)
            ->setParameter('restaurant', $restaurant);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User       $user
     * @param Restaurant $restaurant
     * @param int        $page
     *
     * @return Pagerfanta
     */
    public function getPagedLeadersPhotos(User $user, Restaurant $restaurant, $page = 1)
    {
        $leadersId = $this->getLeadersOf($user->getId());
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder();
        $queryBuilder
            ->select('p,u')
            ->from(Photo::SHORTCUT_CLASS_NAME, 'p')
            ->leftJoin('p.user', 'u')
            ->andWhere('p.restaurant = :restaurant')
            ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
            ->setParameter('restaurant', $restaurant)
            ->setParameter('leadersId', $leadersId);

        return  $this->getPager($queryBuilder, $page);
    }
}