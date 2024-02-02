<?php

namespace Adamski\Symfony\PhoneNumberBundle\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PhoneNumber extends Constraint {
    public string $message = "Phone number is not valid";
}
