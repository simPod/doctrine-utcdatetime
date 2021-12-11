# Doctrine UTCDateTimeType

[![GitHub Actions][GA Image]][GA Link]
[![Shepherd Type][Shepherd Image]][Shepherd Link]
[![Code Coverage][Coverage Image]][CodeCov Link]
[![Downloads][Downloads Image]][Packagist Link]
[![Packagist][Packagist Image]][Packagist Link]

[GA Image]: https://github.com/simPod/doctrine-utcdatetime/workflows/CI/badge.svg

[GA Link]: https://github.com/simPod/doctrine-utcdatetime/actions?query=workflow%3A%22CI%22+branch%3Amaster

[Shepherd Image]: https://shepherd.dev/github/simPod/doctrine-utcdatetime/coverage.svg

[Shepherd Link]: https://shepherd.dev/github/simPod/doctrine-utcdatetime

[Coverage Image]: https://codecov.io/gh/simPod/doctrine-utcdatetime/branch/master/graph/badge.svg

[CodeCov Link]: https://codecov.io/gh/simPod/doctrine-utcdatetime/branch/master

[Downloads Image]: https://poser.pugx.org/simPod/doctrine-utcdatetime/d/total.svg

[Packagist Image]: https://poser.pugx.org/simPod/doctrine-utcdatetime/v/stable.svg

[Packagist Link]: https://packagist.org/packages/simPod/doctrine-utcdatetime

Contains DateTime and DateTimeImmutable Doctrine DBAL types that store datetimes in UTC timezone (`TIMESTAMP` type in postgres).

For more detailed explanation see [Doctrine ORM docs](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/working-with-datetime.html#handling-different-timezones-with-the-datetime-type) and [this comment](https://github.com/simPod/doctrine-utcdatetime/issues/6#issuecomment-722343092).

For more info about usage in Doctrine ORM see [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/working-with-datetime.html). The code is mostly copied from there.

## Using the UTCDateTimeType

### Installation

```sh
composer require simpod/doctrine-utcdatetime
```

### Overriding default types in Symfony

Simply copied from DoctrineExtensions' documentation:

``` yaml
doctrine:
    dbal:
        types:
            datetime: SimPod\DoctrineUtcDateTime\UTCDateTimeType
            datetimetz: SimPod\DoctrineUtcDateTime\UTCDateTimeType
            datetime_immutable: SimPod\DoctrineUtcDateTime\UTCDateTimeImmutableType
            datetimetz_immutable: SimPod\DoctrineUtcDateTime\UTCDateTimeImmutableType
```
