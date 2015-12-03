<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class BaseValidator
 *
 * @package Tastd\Bundle\CoreBundle\Validator\Constraints
 */
abstract class BaseValidator extends ConstraintValidator
{
    public function addViolation($message, $parameters)
    {
        // If you're using the old 2.4 validation API
        $this->context->addViolation(
            $message,
            $parameters
        );
    }
} 