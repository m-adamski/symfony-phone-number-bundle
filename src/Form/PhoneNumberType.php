<?php

namespace Adamski\Symfony\PhoneNumberBundle\Form;

use Adamski\Symfony\PhoneNumberBundle\Form\DataTransformer\PhoneNumberToStringTransformer;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneNumberType extends AbstractType {
    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct() {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add("country", ChoiceType::class, [
                "label"             => false,
                "choices"           => $this->generateChoices($options["countries"], $options["countries_renderer"]),
                "preferred_choices" => $this->getPreferredChoices($options["preferred"]),
                "attr"              => $options["country_attr"],
                "required"          => true
            ])
            ->add("number", TextType::class, [
                "label"    => false,
                "attr"     => $options["number_attr"],
                "required" => $options["required"]
            ])
            ->addViewTransformer(new PhoneNumberToStringTransformer());
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            "countries"          => [],
            "countries_renderer" => null,
            "preferred"          => [],
            "country_attr"       => [],
            "number_attr"        => [],
            "error_bubbling"     => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string {
        return "phone_number";
    }

    /**
     * Generate countries.
     *
     * @param array         $customChoices
     * @param callable|null $renderer
     * @return array
     */
    private function generateChoices(array $customChoices, ?callable $renderer): array {
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
            if (is_callable($renderer)) {
                $rendererResult = call_user_func($renderer, $country["countryName"], $country["countryCode"], $country["regionCode"]);
                $responseArray[$rendererResult] = $country["regionCode"];
            } else {
                $responseArray[sprintf("%s (+%d)", $country["countryName"], $country["countryCode"])] = $country["regionCode"];
            }
        }

        return $responseArray;
    }

    /**
     * Translate names of specified countries.
     *
     * @param array $countries
     * @return array
     */
    private function translateCountries(array $countries): array {
        $responseArray = [];

        foreach ($countries as $country) {
            $regionCode = strtoupper($country["regionCode"]);

            try {
                $country["countryName"] = Countries::getName($regionCode);
            } catch (\Exception $exception) {
                $country["countryName"] = $regionCode;
            };

            $responseArray[] = $country;
        }

        return $responseArray;
    }

    /**
     * Generate array with preferred choices.
     *
     * @param string|array $customChoices
     * @return array
     */
    private function getPreferredChoices(mixed $customChoices): array {
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
