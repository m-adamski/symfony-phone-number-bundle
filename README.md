# PhoneNumber Bundle for Symfony 4

Custom PhoneNumber Bundle integrating [libphonenumber](https://github.com/giggsey/libphonenumber-for-php) library into Symfony 4 project. This bundle is inspired by [PhoneNumberBundle](https://github.com/misd-service-development/phone-number-bundle) but simplified and created for own projects.

## Installation

Use Composer to install this bundle into Symfony 4 project:

```
$ composer require m-adamski/symfony-phone-number-bundle
```

## Configuration

Register new Doctrine Type in ``config/packages/doctrine.yaml``

```(yaml)
doctrine:
    dbal:
        types:
            phone_number: Adamski\Symfony\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType
```

This Bundle provide type template for Bootstrap 4. Register it in ``config/packages/twig.yaml``

```(yaml)
twig:
    form_themes:
        - '@PhoneNumber/Form/phone_number_widget.html.twig'
```

You can also overwrite default Symfony Bootstrap 4 template by adding ``- '@PhoneNumber/Form/bootstrap_4_layout.html.twig'`` into ``form_themes`` parameter:

```(yaml)
twig:
    form_themes:
        - '@PhoneNumber/Form/bootstrap_4_layout.html.twig'
        - '@PhoneNumber/Form/phone_number_widget.html.twig'
```

## How to use it?

Bundle provide additional Doctrine Type and Form Type. First, edit entity to use PhoneNumber Type:

```(php)
use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;
use Adamski\Symfony\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

/**
 * @var string
 * @AssertPhoneNumber
 * @ORM\Column(name="phone_number", type="phone_number", nullable=true)
 */
protected $phoneNumber;
```

Now it's time to provide changes in Form Type:

```(php)
use Adamski\Symfony\PhoneNumberBundle\Form\PhoneNumberType;

public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add("phoneNumber", PhoneNumberType::class, [
            "label"     => "Phone number",
            "preferred" => "PL",
            "required"  => false
        ]);
}
```

## License

MIT
