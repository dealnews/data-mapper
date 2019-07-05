# Data Mapper

This library provides an interface and an abstract base class for creating
data mapper objects.

It also provides a Repository class based on
[dealnews/repository](https://github.com/dealnews/data-repository)
which is tailored for use with Data Mapper classes.

## The Data Mapper Pattern

From [Wikipedia](https://en.wikipedia.org/wiki/Data_mapper_pattern):

A Data Mapper is a Data Access Layer that performs bidirectional transfer of
data between a persistent data store (often a relational database) and an
in-memory data representation (the domain layer). The goal of the pattern is
to keep the in-memory representation and the persistent data store independent
of each other and the data mapper itself. The layer is composed of one or
more mappers (or Data Access Objects), performing the data transfer. Mapper
implementations vary in scope. Generic mappers will handle many different
domain entity types, dedicated mappers will handle one or a few.

## Why use the Data Mapper pattern?

Consider a world where an application needs to move data from one storage
system to another. With data mappers, the application does not need to
understand the different storage layers.

```php
$db_mapper = new \DB\Widget\Mapper();

// Load a Widget from a database
$widget = $db_mapper->load(1);

// Save the Widget to some external storage
// like a 3rd party API
$external_mapper = new \ExternalAPI\Widget\Mapper();
$widget = $external_mapper->save($widget);
```

## Using the Repository

The Repository allows for an application to define in one place the classes
that are mapped using mappers. This reduces duplicate code and allows for a
greater level of abstraction within the application.

```php
# Application\Repository.php
$repo = new \DealNews\DataMapper\Repository();
$repo->add_mapper("Widget", new \DB\Widget\Mapper());
```
```php
# Application\SomeFile.php
require "Repository.php";

$widget = $repo->new("Widget");

$widget = $repo->save("Widget", $widget);
```
