<?php

namespace Tamble\Bluedrone\Api\Token\Storage;

use Tamble\Bluedrone\Api\Token\Token;

class Memcached implements StorageInterface
{
    protected $key;

    protected $persistentId;

    protected $host;

    protected $port;

    protected $memcached;

    /**
     * @param string            $key                     The key to use for the token
     * @param string|\Memcached $persistentIdOrMemcached The memcached persistent id or the Memcached instance. The persistent id can be null if connection reuse is not desired.
     * @param string            $host                    The host for the memcached connection. If a Memcached instance is passed, it can be empty.
     * @param string            $port                    The port for the memcached connection. If a Memcached instance is passed, it can be empty.
     */
    public function __construct(
        $key,
        $persistentIdOrMemcached = null,
        $host = null,
        $port = null
    ) {
        $this->key = $key;

        if ($persistentIdOrMemcached instanceof \Memcached) {
            $this->memcached = $persistentIdOrMemcached;
        } else {
            $this->persistentId = $persistentIdOrMemcached;
            $this->host = $host;
            $this->port = $port;
        }
    }

    /**
     * @return Token|false
     */
    public function fetchToken()
    {
        $memcached = $this->getMemcached();
        if ($tokenData = $memcached->get($this->key)) {
            return new Token($tokenData['value'], (int)$tokenData['eol_timestamp']);
        }

        return false;
    }

    /**
     * @param Token $token
     *
     * @return bool
     * @throws \UnexpectedValueException
     */
    public function storeToken(Token $token)
    {
        $memcached = $this->getMemcached();
        return $memcached->set(
            $this->key,
            array('value' => $token->getValue(), 'eol_timestamp' => $token->getEolTimestmap())
        );
    }

    /**
     * @return \Memcached
     */
    public function getMemcached()
    {
        if ($this->memcached === null) {
            $this->memcached = new \Memcached($this->persistentId);
            $servers = $this->memcached->getServerList();

            $shouldAddServer = true;
            if (!empty($servers)) {
                foreach ($servers as $server) {
                    if ($server['host'] == $this->host && $server['port'] == $this->port) {
                        $shouldAddServer = false;
                    }
                }
            }

            if ($shouldAddServer) {
                $this->memcached->addServer($this->host, $this->port);
            }
        }

        return $this->memcached;
    }
}
