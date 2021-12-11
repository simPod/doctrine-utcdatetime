<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use Doctrine\DBAL\Types\DateTimeType;
use SimPod\DoctrineUtcDateTime\UTCDateTimeType;

final class UTCDateTimeTypeTest extends DateTimeTypeTestCaseBase
{
    protected function type(): DateTimeType
    {
        return new UTCDateTimeType();
    }
}
