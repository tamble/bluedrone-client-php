<?php

namespace Tamble\Bluedrone\Api;

require('../vendor/autoload.php');

$validator = new HookValidator('CLIENT_SECRET');

if ($validator->isValid()) {
    // The request has been verified that it comes from BlueDrone

    // do you normal processing here ...
} else {
    // log it maybe?
}