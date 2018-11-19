<?php

namespace Adamski\Symfony\PhoneNumberBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PhoneNumber
 *
 * @Annotation
 * @package Adamski\Symfony\PhoneNumberBundle\Validator\Constraints
 */
class PhoneNumber extends Constraint {

    /**
     * @var string
     */
    protected $message = "Provided phone number is incorrect";

    /**
     * {@inheritdoc}
     */
    public function validatedBy() {
        return PhoneNumberValidator::class;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
}
