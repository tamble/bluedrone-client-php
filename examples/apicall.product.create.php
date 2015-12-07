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