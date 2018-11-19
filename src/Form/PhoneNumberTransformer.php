<?php

namespace Adamski\Symfony\PhoneNumberBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PhoneNumberTransformer implements DataTransformerInterface {

    /**
     * {@inheritdoc}
     */
    public function transform($value) {

        if (!$value) {
            return ["country" => "", "number" => ""];
        }

        if ($value && !$value instanceof PhoneNumber) {
            throw new TransformationFailedException("Expected instance of Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber class");
        }

        return [
            "country" => $value->getRegionCode(),
            "number"  => $value->formatNational()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value) {

        if (!$value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException("Expected array");
        }

        if (trim($value["number"]) == "") {
            return null;
        }

        return PhoneNumber::make($value["number"], $value["country"]);
    }
}
