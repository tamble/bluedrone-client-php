<?php

namespace Tamble\Bluedrone\Api;

use Tamble\Bluedrone\Api\Token\Storage\Pdo\Mysql;

require('../vendor/autoload.php');

/*
 * Example is using a PDO instance instead of passing all the parameters to the mysql adapter
 */
$pdo = new \PDO('mysql:dbname=database;host=127.0.0.1', 'user', 'password');
$storage = new Mysql('bluedrone_tokens', $pdo);

$client = new Client('CLIENT_ID', 'CLIENT_SECRET', $storage);

try {
    $client->createOrUpdateOrder(
        '23',
        '789',
        array(
            "order_date" => "2014-06-19T19:12:32Z",
            "ship_to_is_company" => false,
            "ship_to_name" => "John Green",
            "ship_to_country" => "US",
            "ship_to_state" => "MN",
            "ship_to_city" => "St. Louis Park",
            "ship_to_zip" => "55416",
            "ship_to_address1" => "3540 Dakota Ave. S.",
            "ship_to_address2" => "",
            "ship_to_email" => "john@bluedrone.com",
            "ship_to_phone" => "1-800-594-4730",
            "ship_to_fax" => "",
            "notes_from_customer" => "Please deliver after 4 PM.",
            "notes_to_customer" => "The package is wrapped in additional material in order to protect it from damage.",
            "product_subtotal" => 18.5,
            "shipping" => 5,
            "discount" => 3,
            "tax" => 0,
            "order_total" => 25.5,
            "currency_code" => "USD",
            "order_lines" => array(
                array(
                    "sku" => "12345-09-M-XL-WHT",
                    "quantity" => 2,
                    "price" => 9
                ),
                array(
                    "sku" => "2345-09-F-RED",
                    "quantity" => 1,
                    "price" => 5
                )
            )
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