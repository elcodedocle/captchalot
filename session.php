<?php

namespace info\synapp\tools\captcha;

use \Exception;
use \PDOException;
use \PDO;

class session implements sessioninterface {
    
    /**
     * @var \PDO $dbh
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
     * @return mixed
     */
    public function install(){

        $stmt = $this->dbh->prepare(
            'CREATE TABLE IF NOT EXISTS '.$this->tableName
            . ' (nonce VARCHAR(255), value VARCHAR(255),'
            . ' session VARCHAR(255), ip VARCHAR(255))'
        );
        return $stmt->execute();

    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function getCaptcha($uuid){

        $stmt = $this->dbh->prepare(
            'SELECT * FROM '.$this->tableName.' WHERE nonce = ?'
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
    public function addCaptcha($uuid,$word,$ip,$sessionId=null){
        if ($sessionId===null){
            $sessionId = $this->sessionId;
        }
        $stmt = $this->dbh->prepare(
            'INSERT INTO '.$this->tableName.' VALUES (?,?,?,?)'
        );
        return $stmt->execute(array($uuid,$word,$sessionId,$ip));
        
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function removeCaptcha($uuid){
        
        $stmt = $this->dbh->prepare(
            'DELETE FROM '.$this->tableName.' WHERE nonce = ?'
        );
        return $stmt->execute(array($uuid));
        
    }

    /**
     * @param $sessionId
     * @param null|PDO $dbh
     * @param null|\info\synapp\tools\captcha\dbparams $dBParams
     * @param string $tableName
     * @throws \Exception
     */
    public function __construct($sessionId, $dbh = null, $dBParams = null, $tableName = 'captchalot'){

        if (!isset($sessionId)||!is_string($sessionId)||strlen($sessionId)<1){
            throw new Exception(
                'Invalid session id.',
                500
            );
        }
        $this->sessionId = $sessionId;
        if ($dbh!==null){
            $this->dbh = $dbh;
        } else {
            if ($dBParams===null){
                $config = array();
                require 'config.php';
                require 'dbparams.php';
                $dBParams = new dbparams($config['dBHost'],$config['dBPort'],$config['dBUser'],$config['dBPassword'],$config['dBName']);
                $this->tableName = $config['tableName'];
            }
            
            try {
                $this->dbh = new PDO(
                    "mysql:host=".$dBParams->getDBHost().
                    ";port=".$dBParams->getDBPort().
                    ";dbname=".$dBParams->getDBName().
                    ";charset=utf8",
                    $dBParams->getDBUser(),
                    $dBParams->getDBPassword(),
                    array(
                        PDO::ATTR_PERSISTENT => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    )
                );
                $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception(
                    'Unable to connect to database',
                    500
                );
            }
        }
        
    }
    
} 