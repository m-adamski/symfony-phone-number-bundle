<?php

namespace Adamski\Symfony\PhoneNumberBundle\Validator;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneNumberValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        if (null === $value || "" === $value) {
            return;
        }

        try {
            $phoneNumber = $phoneNumberUtil->parse($value);

            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                $this->addViolation($constraint);
            }
        } catch (NumberParseException $exception) {
            $this->addViolation($constraint);
        }
    }

    /**
     * @param Constraint $constraint
     * @return void
     */
    private function addViolation(Constraint $constraint): void {
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
