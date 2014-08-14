<?php

namespace Tamble\Bluedrone\Api;

class HookValidator
{

    protected $clientSecret;

    public function __construct($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    public function isValid()
    {
        $postBody = file_get_contents('php://input');
        $headerHash = $_SERVER['HTTP_X_BLUEDRONE_HOOK_SIGNATURE'];
        $postHash = hash_hmac('SHA256', $postBody, $this->clientSecret);

        return $headerHash === $postHash;
    }
}