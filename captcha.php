<?php
namespace info\synapp\tools\captcha;

use \Exception;

/**
 * Class captcha
 * @package info\synapp\tools\captcha
 */
class captcha {

    /**
     * @var sessioninterface
     */
    private $session;

    /**
     * @var null|\info\synapp\tools\uuid\uuid
     */
    private $uuidGenerator;

    /**
     * @var null|\info\synapp\tools\captcha\captchaword
     */
    private $wordGenerator;

    /**
     * @var null|captchaimage
     */
    private $imageGenerator;

    /**
     * @var string
     */
    private $ip;

    /**
     * Echoes the image
     * 
     * @return string $uuid The $uuid of the word echoed on the image
     * @throws \Exception
     */
    public function echoImage(){

        $uuid = $this->uuidGenerator->v4();
        $word = $this->wordGenerator->generateWord();
        $image = $this->imageGenerator->createImage($word);
        if (isset($image)||$image!==false){
            if (!imagepng($image)){
                throw new Exception(
                    'Error generating png from captcha image',
                    500
                );
            } else {
                $this->persist($uuid, $word);
                return $uuid;
            }
        } else {
            throw new Exception(
                'There is no image to output.',
                500
            );
        }
        
    }

    /**
     * Returns an array containing both the base64 encoded png 
     * image html src attr value string and the image uuid string
     * 
     * @return array
     * @throws \Exception
     */
    public function getCaptchaUuidAndImgBase64SrcAttrVal(){
        
        $uuid = $this->uuidGenerator->v4();
        $word = $this->wordGenerator->generateWord();
        $image = $this->imageGenerator->createImage($word);
        if (isset($image)&&$image!==false){
            ob_start();
            imagepng($image);
            $base64Image = base64_encode(ob_get_clean());
            error_log(var_export($base64Image,true));
            $this->persist($uuid, $word);
            return array(
                'captchaId' => $uuid,
                'base64CaptchaImage' => 'data:image/png;base64,' . $base64Image
            );
        } else {
            throw new Exception(
                'There is no image to output.',
                500
            );
        }
        
    }

    /**
     * @param string $uuid
     * @param string $word
     * @param null|bool|string $ip set to false to skip ip validation, null 
     * for value set in construct
     * @return bool
     */
    public function validate($uuid, $word, $ip = null){
        if ($ip === null){
            $ip = $this->ip;
        }
        if ($ip === false){
            $ip = null;
        }
        if (isset($uuid) && isset($word)){
            if (!($captcha=$this->read($uuid))){
                return false;
            } else {
                if ($ip!==null && $captcha['ip']!==$ip){
                    return false;
                }
                if ($captcha['value']!==hash(
                        'sha256',
                        trim(preg_replace ( '/ +/' , ' ' , $word))
                    )
                ){
                    $this->session->removeCaptcha($uuid);
                    return false;
                }
                $this->session->removeCaptcha($uuid);
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param \info\synapp\tools\captcha\sessioninterface $session
     * @param string $ip
     * @param null|\info\synapp\tools\uuid\uuid $uuidGenerator
     * @param null|\info\synapp\tools\captcha\captchaword $wordGenerator
     * @param null|\info\synapp\tools\captcha\captchaimage $imageGenerator
     * @throws \Exception
     */
    public function __construct($session, $ip = null, $uuidGenerator = null, $wordGenerator = null, $imageGenerator = null){

        if (!isset($session)){
            throw new Exception(
                'No $session given to captcha constructor.',
                500
            );
        }
        $this->session = $session;
        
        if (is_string($ip)){
            $this->ip = $ip;
        } else {
            $this->ip = null;
        }
        
        if (!isset($uuidGenerator)){
            throw new Exception(
                'No uuid generator and validator given to captcha constructor.',
                500
            );
        }
        $this->uuidGenerator = $uuidGenerator;
        
        if (isset($wordGenerator)){
            $this->wordGenerator = $wordGenerator;
        }
        
        if (isset($imageGenerator)){
            $this->imageGenerator = $imageGenerator;
        }

    }

    /**
     * destructor (destroys the image)
     */
    public function __destruct(){
        $this->imageGenerator->destroyImage();
    }

    /**
     * @param string $uuid
     * @param $word
     */
    private function persist($uuid,$word){
        $this->session->addCaptcha(
            $uuid,
            hash('sha256',trim(preg_replace('/ +/',' ',$word))),
            $this->ip
        );
    }

    /**
     * Connects to DB to read the captcha
     *
     * @param string $uuid
     * @return mixed
     */
    private function read($uuid){
        return $this->session->getCaptcha($uuid);
    }
    
}
