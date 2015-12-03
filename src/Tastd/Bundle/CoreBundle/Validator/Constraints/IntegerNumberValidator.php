<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

/**
 * Class IntegerNumberValidator
 *
 * @package Tastd\Bundle\CoreBundle\Validator\Constraints
 */
class IntegerNumberValidator extends BaseUseRegexValidator
{
    public function getPattern()
    {
        return "/^[0-9]+$/";
    }
}