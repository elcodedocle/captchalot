<?php

namespace info\synapp\tools\captcha;

interface sessioninterface {

    /**
     * @param string $uuid
     * @return mixed
     */
    public function getCaptcha($uuid);

    /**
     * @param string $uuid
     * @param string $word
     * @param string $sessionUuid
     * @param string $ip
     * @return bool
     */
    public function addCaptcha($uuid,$word,$ip,$sessionUuid = null);

    /**
     * @param string $uuid
     * @return bool
     */
    public function removeCaptcha($uuid);
    
} 