<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use InvalidArgumentException;
use Throwable;

use function is_string;
use function str_contains;

final class UTCDateTimeImmutableType extends DateTimeImmutableType
{
    private static DateTimeZone|null $utc = null;

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|null
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
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeImmutable|null
    {
        if ($value === null || $value instanceof DateTimeImmutable) {
            return $value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        $format = $platform->getDateTimeFormatString();

        if ($format === 'Y-m-d H:i:s.u' && ! str_contains($value, '.')) {
            $value .= '.0';
        }

        $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $value);

        if ($dateTime !== false) {
            return $dateTime;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $e) {
            throw InvalidFormat::new(
                $value,
                self::class,
                $platform->getDateTimeFormatString(),
                $e,
            );
        }
    }

    private static function utc(): DateTimeZone
    {
        if (self::$utc === null) {
            self::$utc = new DateTimeZone('UTC');
        }

        return self::$utc;
    }
}
