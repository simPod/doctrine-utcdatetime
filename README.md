# Doctrine UTCDateTimeType

[![GitHub Actions][GA Image]][GA Link]
[![Code Coverage][Coverage Image]][CodeCov Link]
[![Downloads][Downloads Image]][Packagist Link]
[![Packagist][Packagist Image]][Packagist Link]

[GA Image]: https://github.com/simPod/doctrine-utcdatetime/workflows/CI/badge.svg

[GA Link]: https://github.com/simPod/doctrine-utcdatetime/actions?query=workflow%3A%22CI%22+branch%3Amaster

[Coverage Image]: https://codecov.io/gh/simPod/doctrine-utcdatetime/branch/master/graph/badge.svg

[CodeCov Link]: https://codecov.io/gh/simPod/doctrine-utcdatetime/branch/master

[Downloads Image]: https://poser.pugx.org/simPod/doctrine-utcdatetime/d/total.svg

[Packagist Image]: https://poser.pugx.org/simPod/doctrine-utcdatetime/v/stable.svg

[Packagist Link]: https://packagist.org/packages/simPod/doctrine-utcdatetime

Contains DateTime and DateTimeImmutable Doctrine DBAL types that store datetimes in UTC timezone (`TIMESTAMP` type in postgres).

Requires Doctrine DBAL 4.5 or newer. DBAL 4.5 provides built-in UTC types, so new applications can use those without installing this package. For applications still using this package, see [Migrating to DBAL's built-in UTC types](#migrating-to-dbals-built-in-utc-types).

For more detailed explanation see [Doctrine ORM docs](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/working-with-datetime.html#handling-different-timezones-with-the-datetime-type) and [this comment](https://github.com/simPod/doctrine-utcdatetime/issues/6#issuecomment-722343092).

For more info about usage in Doctrine ORM see [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/working-with-datetime.html). The code is mostly copied from there.

## Using the UTCDateTimeType

### Installation

```sh
composer require simpod/doctrine-utcdatetime
```

### Overriding default types in Symfony

``` yaml
doctrine:
    dbal:
        types:
            datetime: SimPod\DoctrineUtcDateTime\UTCDateTimeType
            datetime_immutable: SimPod\DoctrineUtcDateTime\UTCDateTimeImmutableType
```

## Migrating to DBAL's built-in UTC types

Doctrine DBAL 4.5 includes `datetime_utc` and `datetime_utc_immutable`. These types normalize values to UTC on write and interpret database values as UTC on read. To stop using this package:

1. Update to `doctrine/dbal:^4.5`.
2. Find fields that rely on the overrides above. Change fields mapped as `datetime` to `datetime_utc`, and fields mapped as `datetime_immutable` to `datetime_utc_immutable`. For example, change `#[ORM\Column(type: 'datetime_immutable')]` to `#[ORM\Column(type: 'datetime_utc_immutable')]`. In XML or YAML mappings, change the field's `type` in the same way.
3. Remove the `doctrine.dbal.types` overrides shown above, then remove this package with `composer remove simpod/doctrine-utcdatetime`.

If you copied an older version of this example, remove its `datetimetz` and `datetimetz_immutable` overrides too. They pointed to the same plain datetime classes, not to timezone-aware types; review any fields that use those names separately.

Fields left as `datetime` or `datetime_immutable` after removing the overrides use Doctrine's default types and no longer get automatic UTC conversion. DBAL's mutable UTC type also leaves the input `DateTime` unchanged, unlike this package's mutable type, which changes its timezone in place.
