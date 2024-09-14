<?php

namespace info\synapp\tools\captcha;

use Exception;
use PDOException;
use PDO;

class Session implements SessionInterface
{

    /**
     * @var PDO $dbh
     */
    private $dbh;

    /**
     * @var string $sessionId
     */
    private $sessionId;

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @param string $uuid
     * @return mixed
     */
    public function getCaptcha($uuid)
    {

        $stmt = $this->dbh->prepare(
            'SELECT * FROM ' . $this->tableName . ' WHERE nonce = ?'
        );
        $stmt->execute(array($uuid));
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * @param string $uuid
     * @param string $word
     * @param string $ip
     * @param string $sessionId
     * @return bool
     */
    public function addCaptcha($uuid, $word, $ip, $sessionId = null)
    {
        if ($sessionId === null) {
            $sessionId = $this->sessionId;
        }
        $stmt = $this->dbh->prepare(
            'INSERT INTO ' . $this->tableName . ' VALUES (?,?,?,?)'
        );
        return $stmt->execute(array($uuid, $word, $sessionId, $ip));

    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function removeCaptcha($uuid)
    {

        $stmt = $this->dbh->prepare(
            'DELETE FROM ' . $this->tableName . ' WHERE nonce = ?'
        );
        return $stmt->execute(array($uuid));

    }

    /**
     * @param $sessionId
     * @param null|PDO $dbh
     * @param null|DBParams $dBParams
     * @throws Exception
     */
    public function __construct($sessionId, $dbh = null, $dBParams = null)
    {

        if (!isset($sessionId) || !is_string($sessionId) || strlen($sessionId) < 1) {
            throw new Exception(
                'Invalid session id.',
                500
            );
        }
        $this->sessionId = $sessionId;
        if ($dbh !== null) {
            $this->dbh = $dbh;
        } else {
            if ($dBParams === null) {
                $config = array();
                require_once 'config.php';
                $dBParams = new DBParams(
                    $config['dBHost'],
                    $config['dBPort'],
                    $config['dBUser'],
                    $config['dBPassword'],
                    $config['dBName'],
                    $config['dBDriver']);
                $this->tableName = $config['tableName'];
            }

            try {
                $pdoAttributes = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );
                if ($dBParams->getDBDriver() === 'mysql') {
                    $pdoAttributes[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
                    $pdoAttributes[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
                }
                $this->dbh = new PDO(
                    $dBParams->getDBDriver() . ":host=" . $dBParams->getDBHost() .
                    ";port=" . $dBParams->getDBPort() .
                    ";dbname=" . $dBParams->getDBName() .
                    ";charset=utf8",
                    $dBParams->getDBUser(),
                    $dBParams->getDBPassword(),
                    $pdoAttributes
                );
            } catch (PDOException $e) {
                throw new Exception(
                    'Unable to connect to database',
                    500
                );
            }
        }

    }

}
