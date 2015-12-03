<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IntegerNumber extends Constraint
{
    public $message = 'The string "%value%" contains illegal characters. it can only contain integer numbers.';
}