# Doctrine UTCDateTimeType

[![Build Status](https://github.com/simPod/doctrine-utcdatetime/workflows/CI/badge.svg?branch=master)](https://github.com/simPod/doctrine-utcdatetime/actions)
[![Coverage Status](https://coveralls.io/repos/github/simPod/doctrine-utcdatetime/badge.svg?branch=master)](https://coveralls.io/github/simPod/doctrine-utcdatetime?branch=master)
[![Downloads](https://poser.pugx.org/simpod/doctrine-utcdatetime/d/total.svg)](https://packagist.org/packages/simpod/doctrine-utcdatetime)
[![Packagist](https://poser.pugx.org/simpod/doctrine-utcdatetime/v/stable.svg)](https://packagist.org/packages/simpod/doctrine-utcdatetime)
[![Licence](https://poser.pugx.org/simpod/doctrine-utcdatetime/license.svg)](https://packagist.org/packages/simpod/doctrine-utcdatetime)
[![GitHub Issues](https://img.shields.io/github/issues/simPod/doctrine-utcdatetime.svg?style=flat-square)](https://github.com/simPod/doctrine-utcdatetime/issues)
[![Type Coverage](https://shepherd.dev/github/simPod/doctrine-utcdatetime/coverage.svg)](https://shepherd.dev/github/simPod/doctrine-utcdatetime)

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
