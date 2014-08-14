<?php

namespace Tamble\Bluedrone\Api;

class BluedroneException extends \Exception
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $detail;

    /**
     * @param string $title
     * @param int    $status
     * @param string $detail
     */
    public function __construct($title, $status, $detail)
    {
        $this->title = $title;
        $this->status = $status;
        $this->detail = $detail;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param array $array
     *
     * @return BluedroneException
     */
    public static function fromArray(array $array)
    {
        $title = isset($array['title']) ? $array['title'] : '';
        $status = isset($array['status']) ? $array['status'] : 0;
        $detail = isset($array['detail']) ? $array['detail'] : '';
        return new self($title, $status, $detail);
    }
}