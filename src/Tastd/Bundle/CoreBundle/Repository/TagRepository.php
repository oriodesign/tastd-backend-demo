<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Tastd\Bundle\CoreBundle\Entity\Restaurant;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Entity\Tag;

/**
 * Class TagRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class TagRepository extends BaseEntityRepository
{
    /**
     * @param Request $request
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getAll(Request $request)
    {
        $name = $request->query->get('name');
        $like = $request->query->get('like', true) ;
        $like = $like === true || $like === 'true';
        $highlight = $request->query->get('highlight') === 'true';
        $users = $request->query->get('users');
        $geoname = $request->query->get('geoname');
        $groupId = $request->query->get('groupId');
        $leadersOf = $request->query->get('leadersOf');
        $pageNumber = $request->query->get('page', 1);
        $highlightedOrInsertedBy = $request->query->get('user');
        $wish = $request->query->get('wish') === 'true';

        if ($leadersOf) {
            $leadersId = $this->getLeadersOf($leadersOf);
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Tag::SHORTCUT_CLASS_NAME, 't')
            ->orderBy('t.count', 'DESC');

        if (isset($name) && $like) {
            $queryBuilder
                ->andWhere('t.name LIKE :name')
                ->setParameter('name', $name.'%');
        } else if (isset($name)) {
            $queryBuilder
                ->andWhere('t.name = :name')
                ->setParameter('name', $name);
        }


        if ($highlight) {
            $queryBuilder
                ->andWhere('t.highlight = :highlight')
                ->setParameter('highlight', $highlight);
        }

        if (isset($groupId)) {
            $queryBuilder
                ->andWhere('t.groupId = :groupId')
                ->setParameter('groupId', $groupId);
        }

        if (!$wish) {

            $queryBuilder->leftJoin('t.reviews', 'r');

            if (isset($leadersId) || isset($users) || isset($highlightedOrInsertedBy)) {
                $queryBuilder->leftJoin('r.user', 'u');
            }

            if (isset($users)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->in('u.id', ':users'))
                    ->setParameter('users', explode(',', $users));
            }

            if (isset($geoname)) {
                $queryBuilder
                    ->andWhere('r.geoname = :geoname')
                    ->setParameter('geoname', $geoname);
            }

            if (isset($leadersId)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->in('u.id', ':leadersId'))
                    ->setParameter('leadersId', $leadersId);
            }

            if ($highlightedOrInsertedBy) {
                $queryBuilder
                    ->orWhere('u = :user')
                    ->setParameter('user', $highlightedOrInsertedBy)
                    ->orWhere('t.highlight = :highlight')
                    ->setParameter('highlight', true);
            }
        }

        return $this->getPager($queryBuilder, $pageNumber);
    }

    /**
     * @param Restaurant $restaurant
     * @param string     $usersIds
     *
     * @return array
     */
    public function getTagsNamesForRestaurantAndUserIds(Restaurant $restaurant, $usersIds)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.name')
            ->distinct()
            ->from(Review::SHORTCUT_CLASS_NAME, 'r')
            ->join('r.tags', 't')
            ->where('r.restaurant = :restaurant')
            ->andWhere('r.user IN (:users)')
            ->setParameter('users', explode(',', $usersIds))
            ->setParameter('restaurant', $restaurant);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $tagNames
     *
     * @return array
     */
    public function getByNames($tagNames)
    {
        if (count($tagNames) === 0) {
            return array();
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder();

        $queryBuilder
            ->select('t')
            ->from(Tag::SHORTCUT_CLASS_NAME, 't')
            ->andWhere($queryBuilder->expr()->in('t.name', ':tags'))
            ->setParameter('tags', $tagNames);

        return $queryBuilder->getQuery()->getResult();
    }

}