<?php

namespace Tamble\Bluedrone\Api\Token\Storage;

use Tamble\Bluedrone\Api\Token\Token;

class Memory implements StorageInterface
{
    /**
     * @var Token
     */
    protected $token;

    /**
     * @return Token|false
     */
    public function fetchToken()
    {
        if ($this->token) {
            return $this->token;
        }

        return false;
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function storeToken(Token $token)
    {
        $this->token = $token;
        return true;
    }
}
