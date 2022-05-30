<?php

namespace Adamski\Symfony\PhoneNumberBundle\Model;

use Serializable;
use JsonSerializable;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumber as BasePhoneNumber;
use libphonenumber\NumberParseException;

class PhoneNumber implements JsonSerializable, Serializable {

    protected string          $number;
    protected string          $country;
    protected PhoneNumberUtil $phoneNumberUtil;

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
    public static function make(string $number, string $country = PhoneNumberUtil::UNKNOWN_REGION): static {
        return new static($number, $country);
    }

    /**
     * Generate instance of BasePhoneNumber.
     *
     * @return bool|BasePhoneNumber
     */
    public function getPhoneNumberInstance(): BasePhoneNumber|bool {
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
    public function getCountryCode(): ?int {
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
    public function getRegionCode(): string {
        return $this->phoneNumberUtil->getRegionCodeForCountryCode(
            $this->getCountryCode()
        );
    }

    /**
     * Format the phone number in international format.
     *
     * @return string|null
     */
    public function formatInternational(): ?string {
        return $this->format(PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Format the phone number in national format.
     *
     * @return string|null
     */
    public function formatNational(): ?string {
        return $this->format(PhoneNumberFormat::NATIONAL);
    }

    /**
     * Format the phone number in E164 format.
     *
     * @return string|null
     */
    public function formatE164(): ?string {
        return $this->format(PhoneNumberFormat::E164);
    }

    /**
     * Format the phone number in RFC3966 format.
     *
     * @return string|null
     */
    public function formatRFC3966(): ?string {
        return $this->format(PhoneNumberFormat::RFC3966);
    }

    /**
     * Check if phone number is valid.
     *
     * @return bool
     */
    public function isValidNumber(): bool {
        if (false !== ($phoneNumberInstance = $this->getPhoneNumberInstance())) {
            return $this->phoneNumberUtil->isValidNumber($phoneNumberInstance);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(): ?string {
        return $this->formatE164();
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data) {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
        $this->number = $data;
        $this->country = $this->phoneNumberUtil->getRegionCodeForNumber($this->getPhoneNumberInstance());
    }

    /**
     * {@inheritDoc}
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
    private function format(int $format): ?string {
        if (false !== ($phoneNumberInstance = $this->getPhoneNumberInstance())) {
            return $this->phoneNumberUtil->format($phoneNumberInstance, $format);
        }

        return null;
    }
}
