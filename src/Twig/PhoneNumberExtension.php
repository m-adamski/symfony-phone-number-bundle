<?php

namespace Adamski\Symfony\PhoneNumberBundle\Twig;

use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhoneNumberExtension extends AbstractExtension {

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return [
            new TwigFilter("phone_number", [$this, "phoneNumberFilter"])
        ];
    }

    /**
     * Format the phone number according to the pattern provided.
     *
     * @param PhoneNumber $phoneNumber
     * @param string      $format
     * @return string
     */
    public function phoneNumberFilter(PhoneNumber $phoneNumber, string $format = "E164") {

        // Map available formats to functions
        $availableFormat = [
            "E164"          => "formatE164",
            "RFC3966"       => "formatRFC3966",
            "NATIONAL"      => "formatNational",
            "INTERNATIONAL" => "formatInternational"
        ];

        // Uppercase specified format
        $format = strtoupper($format);

        // Check if specified format is available
        if (array_key_exists($format, $availableFormat)) {
            return $phoneNumber->{$availableFormat[$format]}();
        }

        return $phoneNumber->formatE164();
    }
}
