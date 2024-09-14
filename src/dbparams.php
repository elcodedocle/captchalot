<?php

namespace info\synapp\tools\captcha;


class DBParams
{

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
     * @var string $dBDriver
     */
    private $dBDriver;

    /**
     * @param $dBHost
     * @param $dBPort
     * @param $dBUser
     * @param $dBPassword
     * @param $dBName
     * @param $dBDriver
     */
    public function __construct($dBHost, $dBPort, $dBUser, $dBPassword, $dBName, $dBDriver)
    {
        $this->dBHost = $dBHost;
        $this->dBPort = $dBPort;
        $this->dBUser = $dBUser;
        $this->dBPassword = $dBPassword;
        $this->dBName = $dBName;
        $this->dBDriver = $dBDriver;
    }

    /**
     * @return string
     */
    public function getDBUser()
    {
        return $this->dBUser;
    }

    /**
     * @return string
     */
    public function getDBPassword()
    {
        return $this->dBPassword;
    }

    /**
     * @return string
     */
    public function getDBName()
    {
        return $this->dBName;
    }

    /**
     * @return string
     */
    public function getDBHost()
    {
        return $this->dBHost;
    }

    /**
     * @return string|integer
     */
    public function getDBPort()
    {
        return $this->dBPort;
    }

    /**
     * @return string
     */
    public function getDBDriver()
    {
        return $this->dBDriver;
    }

}
