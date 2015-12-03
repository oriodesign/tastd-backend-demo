<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

/**
 * Class HumanKeyValidator
 *
 * @package Tastd\Bundle\CoreBundle\Validator\Constraints
 */
class HumanKeyValidator extends BaseUseRegexValidator
{
    public function getPattern()
    {
        return "/^[a-z0-9_]+$/";
    }
}