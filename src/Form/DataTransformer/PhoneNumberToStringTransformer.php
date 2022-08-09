<?php

namespace Adamski\Symfony\PhoneNumberBundle\Form\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PhoneNumberToStringTransformer implements DataTransformerInterface {
    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct() {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    public function transform(mixed $value): ?array {
        if (null !== $value && "0" !== $value) {
            try {
                if (null !== ($phoneNumber = $this->phoneNumberUtil->parse($value))) {
                    return [
                        "country" => $this->phoneNumberUtil->getRegionCodeForCountryCode($phoneNumber->getCountryCode()),
                        "number"  => $phoneNumber->getNationalNumber()
                    ];
                }
            } catch (NumberParseException $exception) {
                throw new TransformationFailedException("Exception while PhoneNumber transformation");
            }
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function reverseTransform(mixed $value): ?string {
        if (is_array($value) && array_key_exists("country", $value) && array_key_exists("number", $value)) {
            $country = $value["country"];
            $number = $value["number"];

            if (null !== $country && null !== $number) {
                try {
                    $phoneNumber = $this->phoneNumberUtil->parse($number, $country);

                    return $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164);
                } catch (NumberParseException $exception) {
                    throw new TransformationFailedException("Exception while PhoneNumber transformation");
                }
            }
        }

        return null;
    }
}
