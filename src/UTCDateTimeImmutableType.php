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
use function str_contains;

final class UTCDateTimeImmutableType extends DateTimeImmutableType
{
    private static DateTimeZone|null $utc = null;

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
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
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeInterface|null
    {
        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        $format = $platform->getDateTimeFormatString();

        if ($format === 'Y-m-d H:i:s.u' && ! str_contains($value, '.')) {
            $value .= '.0';
        }

        $converted = DateTimeImmutable::createFromFormat(
            $format,
            $value,
            self::utc(),
        );

        if ($converted === false) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $format,
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

    /** {@inheritDoc} */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
