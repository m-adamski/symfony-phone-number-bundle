<?php

namespace Adamski\Symfony\PhoneNumberBundle\Validator;

use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        /** @var \Adamski\Symfony\PhoneNumberBundle\Validator\PhoneNumber $constraint */

        if ($value instanceof PhoneNumber && !$value->isValidNumber()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
