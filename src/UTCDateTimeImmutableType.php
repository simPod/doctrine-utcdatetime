<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use InvalidArgumentException;

use function is_string;

final class UTCDateTimeImmutableType extends DateTimeImmutableType
{
    /** @var DateTimeZone|null */
    private static $utc;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            $value = $value->setTimezone(self::utc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        $converted = DateTimeImmutable::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::utc()
        );

        if ($converted === false) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $converted;
    }

    private static function utc(): DateTimeZone
    {
        if (self::$utc === null) {
            self::$utc = new DateTimeZone('UTC');
        }

        return self::$utc;
    }

    /** {@inheritdoc} */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
