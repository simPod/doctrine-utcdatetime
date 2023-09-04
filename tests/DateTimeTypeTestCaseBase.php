<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Types\DateTimeType;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimPod\DoctrineUtcDateTime\UTCDateTimeType;

abstract class DateTimeTypeTestCaseBase extends TestCase
{
    abstract protected function type(): DateTimeType;

    /** @dataProvider providerConvertToPHPValue */
    public function testConvertToPHPValue(
        DateTimeInterface|null $expected,
        DateTimeInterface|string|null $dbValue,
    ): void {
        $type = $this->type();

        $platform = new class extends PostgreSQL100Platform {
        };

        $phpValue = $type->convertToPHPValue($dbValue, $platform);

        self::assertSame(
            $expected?->format('Y-m-d\TH:i:s.uP'),
            $phpValue?->format('Y-m-d\TH:i:s.uP'),
        );
    }

    /** @return Generator<string, array{DateTimeInterface|null, DateTimeInterface|string|null}> */
    public function providerConvertToPHPValue(): Generator
    {
        yield 'null' => [null, null];

        $now = $this->type() instanceof UTCDateTimeType ? new DateTime() : new DateTimeImmutable();

        yield 'DateTime interface' => [$now, $now];
    }

    /** @dataProvider providerConvertToPHPValueFailsWithInvalidType */
    public function testConvertToPHPValueFailsWithInvalidType(
        mixed $dbValue,
    ): void {
        $type = $this->type();

        $platform = new class extends PostgreSQL100Platform {
        };

        $this->expectException(InvalidArgumentException::class);

        $type->convertToPHPValue($dbValue, $platform);
    }

    /** @return Generator<string, array{mixed}> */
    public function providerConvertToPHPValueFailsWithInvalidType(): Generator
    {
        yield 'int' => [1];
    }

    /** @dataProvider providerConvertToPHPValueSupportsMicroseconds */
    public function testConvertToPHPValueSupportsMicroseconds(DateTimeInterface $expected, string $dbValue): void
    {
        $type = $this->type();

        $platform = new class extends PostgreSQL100Platform {
            public function getDateTimeFormatString(): string
            {
                return 'Y-m-d H:i:s.u';
            }
        };

        $phpValue = $type->convertToPHPValue($dbValue, $platform);

        self::assertSame(
            $expected->format('Y-m-d\TH:i:s.uP'),
            $phpValue->format('Y-m-d\TH:i:s.uP'),
        );
    }

    /** @return Generator<string, array{DateTimeInterface, string}> */
    public function providerConvertToPHPValueSupportsMicroseconds(): Generator
    {
        yield 'timestamp(0)' => [new DateTimeImmutable('2021-12-01 12:34:56.0'), '2021-12-01 12:34:56'];
        yield 'timestamp(3)' => [new DateTimeImmutable('2021-12-01 12:34:56.123'), '2021-12-01 12:34:56.123'];
        yield 'timestamp(6)' => [new DateTimeImmutable('2021-12-01 12:34:56.123456'), '2021-12-01 12:34:56.123456'];
    }
}
