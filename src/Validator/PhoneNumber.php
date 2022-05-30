<?php

namespace Adamski\Symfony\PhoneNumberBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PhoneNumber extends Constraint {
    public string $message = "Provided phone number is incorrect";

    /**
     * {@inheritDoc}
     */
    public function validatedBy(): string {
        return PhoneNumberValidator::class;
    }
}
