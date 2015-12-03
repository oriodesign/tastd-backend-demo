<?php

namespace Tastd\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tastd\Bundle\CoreBundle\Entity\Credential;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\CredentialNotFoundException;

/**
 * Class CredentialRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class CredentialRepository extends EntityRepository
{
    /**
     * @param integer $id
     *
     * @return Credential
     * @throws CredentialNotFoundException
     */
    public function get($id)
    {
        $credential = $this->find($id);
        if (!$credential) {
            throw new CredentialNotFoundException();
        }

        return $credential;
    }
}