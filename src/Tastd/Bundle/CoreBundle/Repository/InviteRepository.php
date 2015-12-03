<?php

namespace Tastd\Bundle\CoreBundle\Repository;

/**
 * Class InviteRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class InviteRepository extends BaseEntityRepository
{
    /**
     * @param string $code
     *
     * @return array
     */
    public function getByCode($code)
    {
        $invites = $this->findBy(array('code'=> $code));

        return $invites;
    }
}