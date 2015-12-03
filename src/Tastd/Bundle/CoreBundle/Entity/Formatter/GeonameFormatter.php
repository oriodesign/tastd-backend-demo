<?php

namespace Tastd\Bundle\CoreBundle\Entity\Formatter;

use Tastd\Bundle\CoreBundle\Entity\Geoname;

class GeonameFormatter implements EntityFormatterInterface
{
    /**
     * @param mixed $entity
     *
     * @return string
     */
    public function getOneLineDescription($entity)
    {
        /** @var Geoname $geoname */
        $geoname = $entity;
        $template = '[%s] <info>%s</info> <fg=yellow>[%s]</> %s <fg=blue>[pop:%s]</> (%s,%s) %s';

        return sprintf($template,
            $geoname->getId(),
            $geoname->getAsciiName(),
            $geoname->getCountry(),
            $geoname->getAdmin1(),
            $geoname->getPopulation(),
            $geoname->getLat(),
            $geoname->getLng(),
            $geoname->getCurrencyCode()
        );
    }

    /**
     * @param $entity
     * @return string
     */
    public function getOneLineShortDescription($entity)
    {
        /** @var Geoname $geoname */
        $geoname = $entity;
        $template = '<info>%s</info> <fg=yellow>[%s]</>';

        return sprintf($template,
            $geoname->getAsciiName(),
            $geoname->getCountry()
        );
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Geoname::CLASS_NAME;
    }
}