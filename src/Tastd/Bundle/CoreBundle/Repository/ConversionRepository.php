<?php

namespace Tastd\Bundle\CoreBundle\Repository;
use Tastd\Bundle\CoreBundle\Entity\Conversion;

/**
 * Class ConversionRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class ConversionRepository extends BaseEntityRepository
{
    /**
     * @param $fingerprint
     * @return Conversion|null
     */
    public function getLastByFingerprint($fingerprint)
    {
        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(Conversion::SHORTCUT_CLASS_NAME, 'c')
            ->where('c.fingerprint = :fingerprint')
            ->andWhere('c.user IS NULL')
            ->setParameter('fingerprint', $fingerprint)
            ->orderBy('c.created', 'DESC')
            ->getQuery()
            ->getResult();

        if (count($results) > 0) {
            return $results[0];
        }

        return null;
    }
}