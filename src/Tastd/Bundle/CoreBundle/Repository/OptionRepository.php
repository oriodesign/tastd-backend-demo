<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Tastd\Bundle\CoreBundle\Entity\Option;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\OptionNotFoundException;

/**
 * Class OptionRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class OptionRepository extends BaseEntityRepository
{

    /**
     * @param integer $id
     *
     * @return Option
     * @throws OptionNotFoundException
     */
    public function get($id)
    {
        $option = $this->find($id);
        if (!$option) {
            throw new OptionNotFoundException();
        }

        return $option;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllByUser(User $user)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('o')
            ->from(Option::SHORTCUT_CLASS_NAME, 'o')
            ->where('o.user = :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();

    }
}