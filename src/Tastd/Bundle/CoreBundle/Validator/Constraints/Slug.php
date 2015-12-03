<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Slug extends Constraint
{
    public $message = 'The string "%value%" contains illegal characters. it can only contain lowercase letters, dashes, underscores or numbers.';

}