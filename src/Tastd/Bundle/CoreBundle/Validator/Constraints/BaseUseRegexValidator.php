<?php

namespace Tastd\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

abstract class BaseUseRegexValidator extends BaseValidator implements BaseUseRegexInterface {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match($this->getPattern(), $value)) {

            $this->addViolation(
                $constraint->message,
                array('%value%' => $value)
            );
        }

    }
}