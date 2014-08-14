<?php

namespace Tamble\Bluedrone\Api\Token\Storage\Pdo;

use Tamble\Bluedrone\Api\Token\Storage\StorageInterface;
use Tamble\Bluedrone\Api\Token\Token;

class Mysql implements StorageInterface
{
    protected $table;

    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $options;

    protected $pdo;

    /**
     * @param string      $table     The table to use for storing the token
     * @param string|\PDO $hostOrPdo The host (string) or the PDO instance
     * @param string      $username  The username for the PDO connection. If a PDO instance is passed, it can be empty.
     * @param string      $password  The password for the PDO connection. If a PDO instance is passed, it can be empty.
     * @param string      $database  The database for the PDO connection. If a PDO instance is passed, it can be empty.
     * @param array       $options   The options for the PDO object. If a PDO instance is passed, it can be empty.
     */
    public function __construct(
        $table,
        $hostOrPdo,
        $username = '',
        $password = '',
        $database = '',
        array $options = array()
    ) {
        $this->table = $table;

        if ($hostOrPdo instanceof \PDO) {
            $this->pdo = $hostOrPdo;
        } else {
            $this->host = $hostOrPdo;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;
            $this->options = $options;
        }
    }

    /**
     * @return Token|false
     */
    public function fetchToken()
    {
        $dbh = $this->getPdo();
        $stmt = $dbh->query("SELECT `value`, `eol_timestamp` FROM {$this->table} LIMIT 1");
        $tokenData = $stmt->fetch();

        if ($tokenData) {
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
        $dbh = $this->getPdo();
        $stmt = $dbh->prepare(
            "REPLACE INTO {$this->table} (`id`,`value`,`eol_timestamp`) VALUES (1,:value,:eol_timestamp)"
        );
        $stmt->execute(array('value' => $token->getValue(), 'eol_timestamp' => $token->getEolTimestmap()));
        $rowCount = $stmt->rowCount();

        if ($rowCount == 0) {
            throw new \UnexpectedValueException("Could not insert token into the '{$this->table}' table.");
        }

        return true;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        if ($this->pdo === null) {
            $this->pdo = new \PDO(
                "mysql:host={$this->host};dbname={$this->database}",
                $this->username,
                $this->password,
                $this->options
            );
        }

        return $this->pdo;
    }
}
