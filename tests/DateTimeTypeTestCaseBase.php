<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeType;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimPod\DoctrineUtcDateTime\UTCDateTimeType;

use function class_exists;
use function date_default_timezone_get;
use function date_default_timezone_set;

abstract class DateTimeTypeTestCaseBase extends TestCase
{
    private function platform(): AbstractPlatform
    {
        if (class_exists('Doctrine\DBAL\Platforms\PostgreSQL120Platform')) {
            // phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName,SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing.IncorrectLinesCountBeforeFirstControlStructure
            return new class extends \Doctrine\DBAL\Platforms\PostgreSQL120Platform {
                public function getDateTimeFormatString(): string
                {
                    return 'Y-m-d H:i:s.u';
                }
            };
        } elseif (class_exists('Doctrine\DBAL\Platforms\PostgreSQL100Platform')) { // for doctrine/dbal 3.0.0
            // phpcs:ignore SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName,SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing.IncorrectLinesCountBeforeFirstControlStructure -- @phpstan-ignore return.type
            return new class extends \Doctrine\DBAL\Platforms\PostgreSQL100Platform {
                public function getDateTimeFormatString(): string
                {
                    return 'Y-m-d H:i:s.u';
                }
            };
        } else {
            return new class extends PostgreSQLPlatform {
                public function getDateTimeFormatString(): string
                {
                    return 'Y-m-d H:i:s.u';
                }
            };
        }
    }

    abstract protected static function type(): DateTimeImmutableType|DateTimeType;

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(
        string|null $expected,
        DateTimeInterface|string|null $dbValue,
    ): void {
        $type = static::type();

        $databaseValue = $type->convertToDatabaseValue($dbValue, $this->platform());

        self::assertSame($expected, $databaseValue);
    }

    /** @return Generator<string, array{string|null, DateTimeInterface|string|null}> */
    public static function providerConvertToDatabaseValue(): Generator
    {
        yield 'null' => [null, null];

        $dateTimeValue = '2000-10-31T01:30:00.000-05:00';
        $dateTime      = static::type() instanceof UTCDateTimeType
            ? new DateTime($dateTimeValue)
            : new DateTimeImmutable($dateTimeValue);

        yield 'DateTime interface' => ['2000-10-31 06:30:00.000000', $dateTime];
    }

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(
        DateTimeInterface|null $expected,
        DateTimeInterface|string|null $dbValue,
    ): void {
        $type = static::type();

        $phpValue = $type->convertToPHPValue($dbValue, $this->platform());

        self::assertSame(
            $expected?->format('Y-m-d\TH:i:s.uP'),
            $phpValue?->format('Y-m-d\TH:i:s.uP'),
        );
    }

    /** @return Generator<string, array{DateTimeInterface|null, DateTimeInterface|string|null}> */
    public static function providerConvertToPHPValue(): Generator
    {
        yield 'null' => [null, null];

        $now = static::type() instanceof UTCDateTimeType ? new DateTime() : new DateTimeImmutable();

        yield 'DateTime interface' => [$now, $now];
        yield 'DateTime formatted' => [$now, $now->format('Y-m-d H:i:s.u')];

        $fallbackValue    = '2026-06-24T12:08:33';
        $fallbackDateTime = static::type() instanceof UTCDateTimeType
            ? new DateTime($fallbackValue, new DateTimeZone('UTC'))
            : new DateTimeImmutable($fallbackValue, new DateTimeZone('UTC'));

        yield 'DateTime constructor formatted' => [$fallbackDateTime, $fallbackValue];
    }

    public function testConvertToPHPValueWithDifferentDefaultTimezone(): void
    {
        $type = static::type();

        $dbValueUtc = $type instanceof UTCDateTimeType
            ? new DateTime('2026-06-24T12:08:33.0', new DateTimeZone('UTC'))
            : new DateTimeImmutable('2026-06-24T12:08:33.0', new DateTimeZone('UTC'));

        $previousTz = date_default_timezone_get();
        date_default_timezone_set('Europe/Athens');

        try {
            $phpValue = $type->convertToPHPValue($dbValueUtc->format('Y-m-d H:i:s'), $this->platform());
        } finally {
            date_default_timezone_set($previousTz);
        }

        self::assertSame(
            $dbValueUtc->format('Y-m-d\TH:i:s.uP'),
            $phpValue->format('Y-m-d\TH:i:s.uP'),
        );
    }

    #[DataProvider('providerConvertToPHPValueFailsWithInvalidType')]
    public function testConvertToPHPValueFailsWithInvalidType(
        mixed $dbValue,
    ): void {
        $type = static::type();

        $this->expectException(InvalidArgumentException::class);

        $type->convertToPHPValue($dbValue, $this->platform());
    }

    /** @return Generator<string, array{mixed}> */
    public static function providerConvertToPHPValueFailsWithInvalidType(): Generator
    {
        yield 'int' => [1];
    }

    #[DataProvider('providerConvertToPHPValueSupportsMicroseconds')]
    public function testConvertToPHPValueSupportsMicroseconds(DateTimeInterface $expected, string $dbValue): void
    {
        $type = static::type();

        $phpValue = $type->convertToPHPValue($dbValue, $this->platform());

        self::assertSame(
            $expected->format('Y-m-d\TH:i:s.uP'),
            $phpValue->format('Y-m-d\TH:i:s.uP'),
        );
    }

    /** @return Generator<string, array{DateTimeInterface, string}> */
    public static function providerConvertToPHPValueSupportsMicroseconds(): Generator
    {
        yield 'timestamp(0)' => [new DateTimeImmutable('2021-12-01 12:34:56.0'), '2021-12-01 12:34:56'];
        yield 'timestamp(3)' => [new DateTimeImmutable('2021-12-01 12:34:56.123'), '2021-12-01 12:34:56.123'];
        yield 'timestamp(6)' => [new DateTimeImmutable('2021-12-01 12:34:56.123456'), '2021-12-01 12:34:56.123456'];
    }
}
