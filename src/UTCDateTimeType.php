<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use InvalidArgumentException;

use function is_string;
use function str_contains;

final class UTCDateTimeType extends DateTimeType
{
    private static ?DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTime) {
            $value->setTimezone(self::utc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTime|null
    {
        if ($value === null || $value instanceof DateTime) {
            return $value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        $format = $platform->getDateTimeFormatString();

        if ($format === 'Y-m-d H:i:s.u' && ! str_contains($value, '.')) {
            $value .= '.0';
        }

        $converted = DateTime::createFromFormat(
            $format,
            $value,
            self::utc()
        );

        if ($converted === false) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $format
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
