<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use SimPod\DoctrineUtcDateTime\UTCDateTimeType;

#[CoversClass(UTCDateTimeType::class)]
final class UTCDateTimeTypeTest extends DateTimeTypeTestCaseBase
{
    protected static function type(): UTCDateTimeType
    {
        return new UTCDateTimeType();
    }
}
