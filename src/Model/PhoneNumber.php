<?php

namespace Adamski\Symfony\PhoneNumberBundle\Model;

use Serializable;
use JsonSerializable;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumber as BasePhoneNumber;
use libphonenumber\NumberParseException;

class PhoneNumber implements JsonSerializable, Serializable {

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * PhoneNumber constructor.
     *
     * @param string $number
     * @param string $country
     */
    public function __construct(string $number, string $country = PhoneNumberUtil::UNKNOWN_REGION) {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneNumberInstance = $this->phoneNumberUtil->parse($number, $country);
        } catch (NumberParseException $exception) {
            $phoneNumberInstance = null;
        }

        if ($phoneNumberInstance) {
            $this->number = $phoneNumberInstance->getNationalNumber();
            $this->country = $this->phoneNumberUtil->getRegionCodeForCountryCode($phoneNumberInstance->getCountryCode());
        }
    }

    /**
     * Create the PhoneNumber instance.
     *
     * @param string $number
     * @param string $country
     * @return static
     */
    public static function make(string $number, string $country = PhoneNumberUtil::UNKNOWN_REGION) {
        return new static($number, $country);
    }

    /**
     * Generate instance of BasePhoneNumber.
     *
     * @return bool|BasePhoneNumber
     */
    public function getPhoneNumberInstance() {
        try {
            return $this->phoneNumberUtil->parse($this->number, $this->country);
        } catch (NumberParseException $exception) {
            return false;
        }
    }

    /**
     * Get Country code.
     *
     * @return int|null
     */
    public function getCountryCode() {
        if (false !== ($phoneNumberInstance = $this->getPhoneNumberInstance())) {
            return $phoneNumberInstance->getCountryCode();
        }

        return null;
    }

    /**
     * Get Region code.
     *
     * @return string
     */
    public function getRegionCode() {
        return $this->phoneNumberUtil->getRegionCodeForCountryCode(
            $this->getCountryCode()
        );
    }

    /**
     * Format the phone number in international format.
     *
     * @return string|null
     */
    public function formatInternational() {
        return $this->format(PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Format the phone number in national format.
     *
     * @return string|null
     */
    public function formatNational() {
        return $this->format(PhoneNumberFormat::NATIONAL);
    }

    /**
     * Format the phone number in E164 format.
     *
     * @return string|null
     */
    public function formatE164() {
        return $this->format(PhoneNumberFormat::E164);
    }

    /**
     * Format the phone number in RFC3966 format.
     *
     * @return string|null
     */
    public function formatRFC3966() {
        return $this->format(PhoneNumberFormat::RFC3966);
    }

    /**
     * Check if phone number is valid.
     *
     * @return bool
     */
    public function isValidNumber() {
        if (false !== ($phoneNumberInstance = $this->getPhoneNumberInstance())) {
            return $this->phoneNumberUtil->isValidNumber($phoneNumberInstance);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize() {
        return $this->formatE164();
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized) {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
        $this->number = $serialized;
        $this->country = $this->phoneNumberUtil->getRegionCodeForNumber($this->getPhoneNumberInstance());
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return $this->formatE164();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->formatE164();
    }

    /**
     * Format the phone number to specified format.
     *
     * @param int $format
     * @return string|null
     */
    private function format(int $format) {
        if (false !== ($phoneNumberInstance = $this->getPhoneNumberInstance())) {
            return $this->phoneNumberUtil->format($phoneNumberInstance, $format);
        }

        return null;
    }
}
