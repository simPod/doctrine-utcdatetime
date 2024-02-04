<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\DateTimeType;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimPod\DoctrineUtcDateTime\UTCDateTimeType;

use function class_exists;

abstract class DateTimeTypeTestCaseBase extends TestCase
{
    private function platform(): AbstractPlatform
    {
        return class_exists('Doctrine\DBAL\Platforms\PostgreSQL100Platform')
            ? new class extends PostgreSQL100Platform {
                public function getDateTimeFormatString(): string
                {
                    return 'Y-m-d H:i:s.u';
                }
            }
            : new class extends PostgreSQLPlatform {
                public function getDateTimeFormatString(): string
                {
                    return 'Y-m-d H:i:s.u';
                }
            };
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
