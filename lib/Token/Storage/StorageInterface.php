<?php

namespace Tamble\Bluedrone\Api\Token\Storage;

use Tamble\Bluedrone\Api\Token\Token;

interface StorageInterface
{
    /**
     * @return Token|false
     */
    public function fetchToken();

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function storeToken(Token $token);
}