<?php

namespace info\synapp\tools\captcha;


class dbparams {

    /**
     * @var string $dBHost
     */
    private $dBHost;

    /**
     * @var string|integer $dBPort
     */
    private $dBPort;

    /**
     * @var string $dBUser
     */
    private $dBUser;
    /**
     * @var string $dBPassword
     */
    private $dBPassword;

    /**
     * @var string $dBName
     */
    private $dBName;

    /**
     * @param $dBHost
     * @param $dBPort
     * @param $dBUser
     * @param $dBPassword
     * @param $dBName
     */
    public function __construct($dBHost, $dBPort, $dBUser, $dBPassword, $dBName){
        $this->dBHost = $dBHost;
        $this->dBPort = $dBPort;
        $this->dBUser = $dBUser;
        $this->dBPassword = $dBPassword;
        $this->dBName = $dBName;
    }

    /**
     * @return string
     */
    public function getDBUser(){
        return $this->dBUser;
    }

    /**
     * @return string
     */
    public function getDBPassword(){
        return $this->dBPassword;
    }

    /**
     * @return string
     */
    public function getDBName(){
        return $this->dBName;
    }

    /**
     * @return string
     */
    public function getDBHost(){
        return $this->dBHost;
    }

    /**
     * @return string|integer
     */
    public function getDBPort(){
        return $this->dBPort;
    }

} 