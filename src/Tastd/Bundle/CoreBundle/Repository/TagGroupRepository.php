<?php

namespace Tastd\Bundle\CoreBundle\Repository;

/**
 * Class TagGroupRepository
 *
 * @package Tastd\Bundle\CoreBundle\Repository
 */
class TagGroupRepository
{
    /** @var array */
    protected $groups;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->groups = array(
            'Best for',        // 0
            'Atmosphere',      // 1
            'Location',        // 2
            'Food',            // 3
            'Drinks',          // 4
            'Services',        // 5
            'Menu',            // 6
            'Other',           // 7
            'Vibe',            // 8
            'Entertainment',   // 9
            'Special Mention', // 10
        );
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param $id|null
     *
     * @return mixed
     */
    public function getGroupNameById($id = null)
    {
        if ($id === null || !isset($this->groups[$id])) {
            return 'Other';
        }

        return $this->groups[$id];
    }

}