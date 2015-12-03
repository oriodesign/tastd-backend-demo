<?php

namespace Tastd\Bundle\CoreBundle\Entity\Formatter;


class EntityFormatterBag
{
    /** @var EntityFormatterInterface[] */
    protected $entityFormatters;

    public function __construct()
    {
        $this->entityFormatters = array();
    }

    public function addEntityFormatter(EntityFormatterInterface $entityFormatterInterface)
    {
        $this->entityFormatters[] = $entityFormatterInterface;
    }

    /**
     * @param $entity
     *
     * @return string
     * @throws \Exception
     */
    public function getOneLineDescription($entity)
    {
        foreach ($this->entityFormatters as $formatter) {
            if (get_class($entity)=== $formatter->getClass()) {
                return $formatter->getOneLineDescription($entity);
            }
        }

        throw new \Exception('Missing entity formatter for class ' . get_class($entity));
    }

    /**
     * @param $entity
     * @return string
     */
    public function getOneLineShortDescription($entity)
    {
        foreach ($this->entityFormatters as $formatter) {
            if (get_class($entity)=== $formatter->getClass()) {
                return $formatter->getOneLineShortDescription($entity);
            }
        }
    }

}