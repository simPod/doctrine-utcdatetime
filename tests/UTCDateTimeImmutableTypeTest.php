<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use Doctrine\DBAL\Types\DateTimeImmutableType;
use PHPUnit\Framework\Attributes\CoversClass;
use SimPod\DoctrineUtcDateTime\UTCDateTimeImmutableType;

#[CoversClass(UTCDateTimeImmutableType::class)]
final class UTCDateTimeImmutableTypeTest extends DateTimeTypeTestCaseBase
{
    protected static function type(): DateTimeImmutableType
    {
        return new UTCDateTimeImmutableType();
    }
}
