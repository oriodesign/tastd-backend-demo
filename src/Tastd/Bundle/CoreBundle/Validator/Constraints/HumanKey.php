<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */
class HumanKey extends Constraint
{
    public $message = 'The string "%value%" contains illegal characters. it can only contain lowercase letters, numbers or underscores.';

}