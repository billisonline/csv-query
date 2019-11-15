# CSV Query

This library lets you "query" CSV files as fluent collection objects. Each row is encapsulated into a row object which makes cells accessible by their column name. Rows are then bundled into an Illuminate `LazyCollection`, exposing functionality like `where`, `filter`, `map`, `reduce`, `count` and more. (Illuminate is the Laravel support library, but Laravel is not required.)

All content is lazy-loaded and the entire CSV is _never_ read into memory, making this library fast and suitable for use with very large CSV files.

## Examples

Iterate through rows and access columns by name:

```php
<?php

foreach ($csv->iterateRows() as $row) {
    $firstName = $row['name/first'];
    $lastName = $row->get('name/last');
    $lastName = $row->get('name/middle', '--'); // Returns "--" by default if there is no "name/middle" column
}
```

Get a collection of rows and use functions like `where` and `count`:

```php
<?php

$seniorsCount = $csv->rows()->where('age', '>=', 65)->count();
```

Consolidate rows with functions like `pluck`:

```php
<?php

$lastNames = $csv->rows()->pluck('name/last');
```

Use aggregate functions like `average`:

```php
<?php

$averageAge = $csv->rows()->average('age');
```

## License
MIT

