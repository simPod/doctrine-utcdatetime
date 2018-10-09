# Doctrine UTCDateTimeType

Contains DateTime and DateTimeImmutable Doctrine DBAL types that store datetimes in UTC timezone.

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
