<?php

namespace Adamski\Symfony\PhoneNumberBundle\Form;

use Exception;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneNumberType extends AbstractType {

    /**
     * @var PhoneNumberUtil
     */
    protected $phoneNumberUtil;

    /**
     * PhoneNumberType constructor.
     */
    public function __construct() {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        // Define global config array for both fields
        $config = [
            "label"              => false,
            "disabled"           => $options["disabled"],
            "translation_domain" => $options["translation_domain"]
        ];

        $builder->add("country", ChoiceType::class, array_merge($config, [
            "choices"           => $this->generateChoices($options["countries"]),
            "preferred_choices" => $this->getPreferredChoices($options["preferred"]),
            "required"          => true
        ]))->add("number", TextType::class, array_merge($config, [
            "required" => $options["required"],
            "attr"     => [
                "placeholder" => $options["placeholder"]
            ]
        ]))->addViewTransformer(
            new PhoneNumberTransformer()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            "countries"       => [],
            "preferred"       => [],
            "selected"        => false,
            "placeholder"     => false,
            "invalid_message" => "Provided phone number is incorrect",
            "by_reference"    => false,
            "error_bubbling"  => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return "phone_number";
    }

    /**
     * Generate choices array.
     *
     * @param array $customChoices
     * @return array
     */
    private function generateChoices(array $customChoices) {

        $countriesArray = [];

        if ($customChoices && count($customChoices) > 0) {
            foreach ($customChoices as $regionCode) {
                $regionCode = strtoupper($regionCode);
                $countryCode = $this->phoneNumberUtil->getCountryCodeForRegion($regionCode);

                if ($countryCode !== 0) {
                    $countriesArray[] = [
                        "countryCode" => $countryCode,
                        "regionCode"  => $regionCode,
                        "countryName" => ""
                    ];
                }
            }
        } else {

            // Generate countries array from default supported regions
            foreach ($this->phoneNumberUtil->getSupportedRegions() as $regionCode) {
                $countryCode = $this->phoneNumberUtil->getCountryCodeForRegion($regionCode);

                $countriesArray[] = [
                    "countryCode" => $countryCode,
                    "regionCode"  => $regionCode,
                    "countryName" => ""
                ];
            }
        }

        // Fill countries array with translated countries names
        $translatedCountries = $this->translateCountries($countriesArray);

        // Generate response array
        $responseArray = [];
        foreach ($translatedCountries as $country) {
            $responseArray[sprintf("%s (+%d)", $country["countryName"], $country["countryCode"])] = $country["regionCode"];
        }

        return $responseArray;
    }

    /**
     * Translate names of specified countries.
     *
     * @param array $countries
     * @return array
     */
    private function translateCountries(array $countries) {
        $responseArray = [];

        foreach ($countries as $country) {
            $regionCode = strtoupper($country["regionCode"]);

            try {
                $country["countryName"] = Countries::getName($regionCode);
            } catch (Exception $exception) {
                $country["countryName"] = $regionCode;
            };

            $responseArray[] = $country;
        }

        return $responseArray;
    }

    /**
     * Generate array with preferred choices.
     *
     * @param $customChoices
     * @return array
     */
    private function getPreferredChoices($customChoices) {
        if ($customChoices) {
            if (is_array($customChoices)) {
                return array_filter($customChoices, function ($regionCode) {
                    return in_array($regionCode, $this->phoneNumberUtil->getSupportedRegions());
                });
            }

            if (is_string($customChoices) && in_array($customChoices, $this->phoneNumberUtil->getSupportedRegions())) {
                return [$customChoices];
            }
        }

        return [];
    }
}
