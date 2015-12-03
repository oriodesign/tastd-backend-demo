<?php

namespace Tastd\Bundle\CoreBundle\Cache;
use Tastd\Bundle\CoreBundle\Entity\Review;

/**
 * Class CacheTag
 *
 * @package Tastd\Bundle\CoreBundle\Cache
 */
final class CacheTag
{
    const REVIEW = 'rv';
    const CONNECTION = 'c';
    const RESTAURANT = 'rs';
    const WISH = 'w';
    const PUSH_MESSAGE = 'pm';
    const OPTION = 'o';
    const GEONAME = 'g';
    const TAG = 't';
    const PHOTO = 'ph';
    const CUISINE = 'cu';

    const LEADER = 'l';
    const FOLLOWER = 'f';
    const USER = 'u';

    const REVIEWED_BY = 'rb';
    const WISHED_BY = 'wb';


    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';
}