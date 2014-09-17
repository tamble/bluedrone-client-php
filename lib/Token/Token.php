<?php

namespace Tamble\Bluedrone\Api\Token;

class Token
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $eolTimestmap;

    /**
     * @param string $value
     * @param int    $eolTimestamp
     */
    public function __construct($value, $eolTimestamp)
    {
        $this->value = $value;
        $this->eolTimestmap = $eolTimestamp;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getEolTimestmap()
    {
        return $this->eolTimestmap;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->eolTimestmap <= time();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}