<?php

namespace Adamski\Symfony\PhoneNumberBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Adamski\Symfony\PhoneNumberBundle\Model\PhoneNumber;

class PhoneNumberType extends Type {

    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string {
        return $platform->getVarcharTypeDeclarationSQL(["length" => 35]);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed {
        if ($value instanceof PhoneNumber) {
            return $value->formatE164();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed {
        if (is_null($value) || $value instanceof PhoneNumber) {
            return $value;
        }

        return PhoneNumber::make($value);
    }

    /**
     * {@inheritDoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string {
        return "phone_number";
    }
}
