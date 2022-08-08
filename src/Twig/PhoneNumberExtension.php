<?php

namespace Adamski\Symfony\PhoneNumberBundle\Twig;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhoneNumberExtension extends AbstractExtension {
    public function getFilters(): array {
        return [
            new TwigFilter("phone_number", [$this, "phoneNumberFilter"])
        ];
    }

    /**
     * @param string $number
     * @param string $format
     * @return string
     */
    public function phoneNumberFilter(string $number, string $format = "E164"): string {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $availableFormat = [
            "E164"          => PhoneNumberFormat::E164,
            "RFC3966"       => PhoneNumberFormat::RFC3966,
            "NATIONAL"      => PhoneNumberFormat::NATIONAL,
            "INTERNATIONAL" => PhoneNumberFormat::INTERNATIONAL
        ];

        // Uppercase specified format
        $format = strtoupper($format);

        try {
            if (null !== ($phoneNumber = $phoneNumberUtil->parse($number))) {
                return $phoneNumberUtil->format($phoneNumber, $availableFormat[$format]);
            }
        } catch (NumberParseException $exception) {
            return $number;
        }

        return $number;
    }
}
