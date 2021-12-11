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

use function assert;

abstract class DateTimeTypeTestCaseBase extends TestCase
{
    abstract protected function type(): DateTimeType;

    /** @dataProvider providerConvertToPHPValue */
    public function testConvertToPHPValue(
        DateTimeInterface|null $expected,
        DateTimeInterface|string|null $dbValue
    ): void {
        $type = $this->type();

        $platform = new class extends PostgreSQL100Platform {
        };

        $phpValue = $type->convertToPHPValue($dbValue, $platform);
        assert($phpValue === null || $phpValue instanceof DateTimeInterface);

        self::assertSame(
            $expected?->format('Y-m-d\TH:i:s.uP'),
            $phpValue?->format('Y-m-d\TH:i:s.uP')
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
        mixed $dbValue
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
}
