<?php

namespace Adamski\Symfony\PhoneNumberBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;

class PhoneNumberType extends Type {

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return $platform->getVarcharTypeDeclarationSQL(["length" => 35]);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if ($value && $value instanceof PhoneNumber) {
            return $value->formatE164();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if (is_null($value) || $value instanceof PhoneNumber) {
            return $value;
        }

        return PhoneNumber::make($value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return "phone_number";
    }
}
