<?php

namespace info\synapp\tools\captcha;

interface SessionInterface
{

    /**
     * @param string $uuid
     * @return mixed
     */
    public function getCaptcha($uuid);

    /**
     * @param string $uuid
     * @param string $word
     * @param string $sessionId
     * @param string $ip
     * @return bool
     */
    public function addCaptcha($uuid, $word, $ip, $sessionId = null);

    /**
     * @param string $uuid
     * @return bool
     */
    public function removeCaptcha($uuid);

}
