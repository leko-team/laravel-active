# Ready-made solution for active an entity with Eloquent models

[![Latest Version](https://img.shields.io/github/release/leko-team/laravel-active.svg?style=flat-square)](https://github.com/leko-team/laravel-active/releases)

This package can activate entity with Eloquent models. It provides a
simple API to work with.

## Installation

The library can be installed via Composer:

```bash
composer require leko-team/laravel-active
```

## Configuration

To be able active eloquent entities you need:

$table->boolean('is_active')->default(false)->index()->comment('Признак активности');
$table->timestamp('start_at')->nullable()->index()->comment('Временная метка начала активности');
$table->timestamp('end_at')->nullable()->index()->comment('Временная метка окончания активности');

* Add migration with columns: `is_active`, `start_at`, `end_at`
* Or assign: `static::IS_ACTIVE`, `static::START_AT`, `static::END_AT` constant with the name of the column you want to use.

```php
php artisan make:migration add_active_columns_in_`your-table`_table
```

```php
    public function up(): void
    {
        Schema::table('your-table', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('your-table', function (Blueprint $table) {
            $table->dropColumn('end_at');
            $table->dropColumn('start_at');
            $table->dropColumn('is_active');
        });
    }
```

If you have existing records in your table you maybe want to update them.

* Add trait `ActivityTrait` to your model.

```php
use ActivityTrait;
```

## Examples

### Base

To activate entity:
```php
$review = Review::first();
$review->activate();
```

To deactivate entity:
```php
$review = Review::first();
$review->deactivate();
```

### Scopes

By default from active entity return only active records with next condition: `Entity is true and start_at IS NULL OR <= then current timestamp ('2023-10-23 13:52:37')
and end_at IS NULL OR end_at >= then current timestamp ('2023-10-23 13:52:37')`
You can change this by applying scope to your Eloquent model.

* withInactive
```php
$review = Review::withInactive()->get();
```
Returns all records.

## Credits

- A big thank you to [Laravel Package](https://www.laravelpackage.com/) for helping out build package with step by step guide.
- [Oleg Kolzhanov](https://github.com/oleg-kolzhanov) for helping with logic.
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
