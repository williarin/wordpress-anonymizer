# WordPress Anonymizer

## Introduction

This repository can be used as a third party library or as a standalone Docker application.

The main use case is to anonymize a database filled with user and customer data before committing it to a VCS repository.
It will anonymize both WordPress base data and WooCommerce data. 

## Docker standalone usage

Run this command to automatically anonymize your WordPress database.

**WARNING!** This operation is irreversible. Make a database backup before proceeding.
To make automatic backups of your WordPress database, you can use [williarin/secure-mysql-backups](https://github.com/williarin/secure-mysql-backups).

```bash
docker run --rm \
    -e DATABASE_URL='mysql://user:user@127.0.0.1:3306/wp_mywebsite?serverVersion=8.0&charset=utf8mb4' \
    williarin/wordpress-anonymizer
```

Variables:

| Variable       | Description                         | Default                                                                      |
|----------------|-------------------------------------|------------------------------------------------------------------------------|
| `DATABASE_URL` | The database url to connect to.     | `mysql://test:test@127.0.0.1:6033/wp_test?serverVersion=8.0&charset=utf8mb4` |
| `TABLE_PREFIX` | The table prefix used by WordPress. | `wp_`                                                                        |


## Installation as a library in your project

To integrate this library to your project, install it with Composer:
```bash
composer require williarin/wordpress-anonymizer
```

### Usage

```php
$faker = Faker\Factory::create();
$connection = DriverManager::getConnection(['url' => 'mysql://user:pass@localhost:3306/wp_mywebsite?serverVersion=8.0']);
$tablePrefix = 'wp_';

$anonymizer = new Anonymizer([
    new UserProvider($connection, $faker, $tablePrefix),
    new UserMetaProvider($connection, $faker, $tablePrefix),
    new CommentProvider($connection, $faker, $tablePrefix),
    new WoocommerceUserMetaProvider($connection, $faker, $tablePrefix),
    new WoocommercePostMetaProvider($connection, $faker, $tablePrefix),
]);

// Anonymize the whole database at once
$anonymizer->anonymize();

// or use a provider to anonymize only a part
$commentProvider = new CommentProvider($connection, $faker, $tablePrefix);
$commentProvider->anonymize();
```

## License

[MIT](LICENSE)

Copyright (c) 2022, William Arin
