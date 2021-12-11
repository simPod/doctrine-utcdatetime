<?php

declare(strict_types=1);

namespace SimPod\DoctrineUtcDateTime\Tests;

use Doctrine\DBAL\Types\DateTimeType;
use SimPod\DoctrineUtcDateTime\UTCDateTimeImmutableType;

final class UTCDateTimeImmutableTypeTest extends DateTimeTypeTestCaseBase
{
    protected function type(): DateTimeType
    {
        return new UTCDateTimeImmutableType();
    }
}
