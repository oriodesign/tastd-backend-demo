<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class RestaurantDistanceConstraint
 *
 * @package Tastd\Bundle\CoreBundle\Validator\Constraints
 */
class RestaurantDistanceConstraint extends Constraint
{
    public $message = 'There is a restaurant nearby with the same name of %string%';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'restaurant_distance_validator';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}