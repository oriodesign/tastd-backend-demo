<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

/**
 * Class SlugValidator
 *
 * @package Tastd\Bundle\CoreBundle\Validator\Constraints
 */
class SlugValidator extends BaseUseRegexValidator
{
    public function getPattern()
    {
        return '/^[a-zA-Z\p{Cyrillic}0-9\s\-\_]+$/u';
    }
}