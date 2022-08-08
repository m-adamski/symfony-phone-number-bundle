# PhoneNumber Bundle for Symfony

Custom PhoneNumber Bundle integrating [libphonenumber](https://github.com/giggsey/libphonenumber-for-php) library into
Symfony project. This bundle is inspired
by [PhoneNumberBundle](https://github.com/misd-service-development/phone-number-bundle) but simplified and created for
own projects.

## Installation

Use Composer to install this bundle into Symfony project:

```
composer require m-adamski/symfony-phone-number-bundle
```

## Configuration

This Bundle provide type template for Bootstrap 5. Register it in ``config/packages/twig.yaml``

```yaml
twig:
    form_themes:
        - '@PhoneNumber/Form/phone_number_bootstrap_5.html.twig'
```

## How to use it?

~~Bundle provide additional Doctrine Type and Form Type. First, edit entity to use PhoneNumber Type:~~

An additional Doctrine Type has been removed from the bundle, because its use caused a problem with logging changes
to the entity. Phone number is now stored in the database as string.

```php
use Adamski\Symfony\PhoneNumberBundle\Validator as AssertPhoneNumber;

#[AssertPhoneNumber\PhoneNumber]
#[ORM\Column(type: "string", nullable: true)]
protected ?string $phoneNumber;
```

Now it's time to provide changes in Form Type:

```php
use Adamski\Symfony\PhoneNumberBundle\Form\PhoneNumberType;

public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add("phoneNumber", PhoneNumberType::class, [
        "label"       => "Phone number",
        "preferred"   => "PL",
        "countries_renderer" => function ($countryName, $countryCode, $regionCode) {
            return sprintf("%s (+%d)", $countryName, $countryCode);
        },
        "placeholder" => "e.g. 123 123 123",
        "required"    => false
    ]);
}
```

There are some additional options that you can use:

- countries - array of available countries e.g. ["PL", "DE"]
- preferred - array or string with preferred country or countries
- placeholder - placeholder content for the number input
- countries_renderer - a function that returns a string displayed as a country selection option. The following
  parameters are available: countryName, countryCode, regionCode

### Twig filter

The displayed phone number in the template can be formatted according to the given pattern.
For this purpose, the Twig ``phone_number`` filter has been implemented.

```twig
{{ current_customer.phoneNumber|phone_number('E164') }}
{{ current_customer.phoneNumber|phone_number('RFC3966') }}
{{ current_customer.phoneNumber|phone_number('NATIONAL') }}
{{ current_customer.phoneNumber|phone_number('INTERNATIONAL') }}
```

## License

MIT
