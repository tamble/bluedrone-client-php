# BlueDrone API PHP Client Library

Bluedrone is a fulfillment service. You can sign up for an account at https://bluedrone.com

Requirements
------------

Any PHP version >= 5.3 is supported.

This client is built on top of the [guzzle http library](http://guzzle.readthedocs.org/en/latest/)
and requires the [json php extension](http://php.net/manual/en/book.json.php).

Having the the [curl php extension](http://php.net/manual/en/book.curl.php) installed too is recommended but not required.


Installation
------------

**Via [Composer](http://getcomposer.org/):**

Create or add the following to composer.json in your project root:
```javascript
{
    "require": {
        "tamble/bluedrone-client-php": "*"
    }
}
```

Install composer dependencies:
```shell
php composer.phar install
```
or
```shell
composer install
```

Require dependencies:
```php
require_once("/path/to/vendor/autoload.php");
```

Storage
-------

Because the Bluedrone API uses Oauth2 you will need to store the access token between requests.
For this purpose two storage adapters are provided but you can use any other as long
as it implements the Token\Storage\StorageInterface interface.

The bundled examples directory display various ways of using these adapters.

If you're using the Mysql adapter, you will require to create a table with the following structure
(the engine can be MyISAM if you want):

```sql
CREATE TABLE IF NOT EXISTS `bluedrone_tokens` (
  `id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `eol_timestamp` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
```

Example
-------

```php
<?php

namespace Tamble\Bluedrone\Api;

use Tamble\Bluedrone\Api\Token\Storage\Pdo\Mysql;

require('../vendor/autoload.php');

/*
 * Example is passing all the data to the storage adapter instead of using a PDO instance
 */
$storage = new Mysql('bluedrone_tokens', '127.0.0.1', 'user', 'password', 'database');

$client = new Client('CLIENT_ID', 'CLIENT_SECRET', $storage);

try {
    $client->createOrUpdateProduct(
        '2345-09-F-RED',
        array(
            "sales_channel_id" => 23,
            "name" => "Gheisa Hair Pin Red",
            "unit_system" => "imperial",
            "weight" => 10,
            "length" => 4,
            "width" => 1,
            "height" => 1,
            "price" => 3.5,
            "currency_code" => "USD",
            "is_unfulfillable" => false,
            "is_fragile" => true,
            "is_dangerous" => false,
            "is_perishable" => false
        )
    );
} catch (BluedroneException $e) {
    /*
     * Any problem that occurs results in an exception being thrown.
     * Each exception offers a 'title', 'details' and 'code' (http status code)
     *
     * The lack of an exception means that the api call was successful.
     */
    echo $e->getTitle();
    echo $e->getDetail();
}
```

Documentation
-------------

Up-to-date API documentation at: http://docs.bluedrone.apiary.io/
