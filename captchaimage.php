<?php

namespace info\synapp\tools\captcha;

use \Exception;

/**
 * Class captchaimage
 * @package info\synapp\tools\captcha
 * 
 * Generates a distorted image (bitmap) from a string of characters
 * 
 * It uses gd library to generate the bitmap.
 */
class captchaimage {

    /**
     * @var array $ttfFileNames
     */
    private $ttfFileNames;

    /**
     * @var int $defaultWidth
     */
    private $defaultWidth;

    /**
     * @var int $defaultHeight
     */
    private $defaultHeight;

    /**
     * Returns an image containing distorted characters provided on $word
     * string, using random fonts from those pointed by $ttfFileNames.
     *
     * @param string $word
     * @param int $width
     * @param int $height
     * @throws \Exception
     * @return resource
     */
    public function createImage($word, $width = null, $height = null){

        if (!is_int($width)){
            $width = $this->defaultWidth;
        }
        if (!is_int($height)){
            $height = $this->defaultHeight;
        }
        if (!is_string($word)||strlen($word)<=0){
            throw new Exception(
                'Invalid word',
                500
            );
        }
        $minMargin = ceil($height/2);
        $wordLen = strlen($word);
        $maxLetterWidthNoWidthOverflow = ceil(($width-$minMargin)/$wordLen);
        $maxLetterWidthNoHeightOverflow = ceil($height/2);
        $letterWidth = min($maxLetterWidthNoHeightOverflow,$maxLetterWidthNoWidthOverflow);
        $extraLeftMarginChars = round(($maxLetterWidthNoWidthOverflow-$letterWidth)/2);
        for ($i=0;$i<$extraLeftMarginChars;$i++){ $word = ' '.$word; }
        $letterSizeMin = ceil(0.95*$letterWidth);
        $letterSizeMax = ceil(1.05*$letterWidth);
        $image = imagecreate($width, $height);
        imagecolorallocate($image, 255, 255, 255); // sets $bg_color 
        $line_colors[] = imagecolorallocate(
            $image,
            0, /* red */
            mt_rand(15, 25), /* green */
            mt_rand(15, 25) /* blue */
        );
        $line_colors[] = imagecolorallocate(
            $image,
            0, /* red */
            mt_rand(15, 25), /* green */
            mt_rand(15, 25) /* blue */
        );
        $line_colors[] = imagecolorallocate(
            $image,
            0, /* red */
            mt_rand(35, 95), /* green */
            mt_rand(35, 95) /* blue */
        );
        $distortion_colors[] = imagecolorallocate(
            $image,
            mt_rand(155, 205), /* red */
            mt_rand(205, 255), /* green */
            mt_rand(205, 255) /* blue */
        );
        $distortion_colors[] = imagecolorallocate(
            $image,
            mt_rand(155, 205), /* red */
            mt_rand(205, 255), /* green */
            mt_rand(205, 255) /* blue */
        );
        $distortion_colors[] = imagecolorallocate(
            $image,
            mt_rand(155, 205), /* red */
            mt_rand(205, 255), /* green */
            mt_rand(205, 255) /* blue */
        );
        $ceilWidth20th = ceil($width/20);
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            imagefilledrectangle(
                $image, /* (0,0) is top left corner */
                $i*20+mt_rand(4, 26), /* x right corner */
                mt_rand(0, $height-1), /* y right corner */
                $i*20-mt_rand(4, 26), /* x left corner */
                mt_rand(0, $height-1), /* y left corner */
                $distortion_colors[mt_rand(0, 2)]
            );
        }
        
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            imageline(
                $image, /* (0,0) is top left corner */
                $i*20+mt_rand(4, 26), /* x right corner */
                0, /* y right corner */
                $i*20-mt_rand(4, 26), /* x left corner */
                $height-1, /* y left corner */
                $line_colors[mt_rand(0, 2)]
            );
        }
        
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            imageline(
                $image,
                $i*20+mt_rand(4, 26), /* x right corner */
                $height-1, /* y right corner */
                $i*20-mt_rand(4, 26), /* x left corner */
                0, /* y left corner */
                $line_colors[mt_rand(0, 2)]
            );
        }
        
        for($i = 0; $i < strlen($word); $i++) {
            imagettftext( /* print the word */
                $image,
                mt_rand($letterSizeMin, $letterSizeMax),
                mt_rand(-20, 20),
                ($i-1)*$letterWidth+mt_rand($letterWidth*0.9, $letterWidth*1.1)+$letterWidth/2,
                mt_rand($height*0.8, $height*0.9),
                $line_colors[mt_rand(0, 1)],
                $this->ttfFileNames[mt_rand(0, 2)],
                $word[$i]
            );
        }
        
        imagesetstyle(
            $image,
            array(
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)],
                $distortion_colors[mt_rand(0, 2)])
        );
        $ceilWidth40th = ceil($width/320);
        for ($i = 0; $i <= $ceilWidth40th; $i++) {
            imageline(
                $image,
                0,
                mt_rand(0, $height-1),
                $width-1,
                mt_rand(0, $height-1),
                IMG_COLOR_STYLED
            );
        }

        $line_starts = array();
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            $line_starts[] = mt_rand(0, $height-1);
        }
        
        $line_ends = array();
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            $line_ends[] = mt_rand(0, $height-1);
        }
        
        for ($i = 0; $i <= $ceilWidth20th; $i++) {
            
            imageline(
                $image,
                $i*20+mt_rand(1, 6), /* x (mostly) right corner */
                $line_starts[$i], /* y (mostly) right corner */
                $i*16+mt_rand(1, 6), /* x (mostly) left corner */
                $line_ends[$i], /* y (mostly) left corner */
                $line_colors[mt_rand(0, 1)]
            );
            
            imageline(
                $image,
                $i*20+mt_rand(1, 6), /* x (mostly) right corner */
                $line_starts[$i], /* y (mostly) right corner */
                $i*16+mt_rand(1, 6), /* x (mostly) left corner */
                $line_ends[$i], /* y (mostly) left corner */
                $line_colors[mt_rand(0, 1)]
            );
            
        }
        
        return $image;
        
    }

    /**
     * Removes the image from memory
     * 
     * @param null|resource $image
     * @return bool
     */
    public function destroyImage($image = null){
        
        if (
            is_resource($image)
        ){
            return imagedestroy($image);
        } else {
            return false;
        }
        
    }

    /**
     * Class constructor
     *
     * @param int $defaultWidth
     * @param int $defaultHeight
     * @param null|array $ttfFileNames
     * @throws \Exception
     */
    public function __construct($defaultWidth = 420, $defaultHeight = 60, $ttfFileNames = null){

        $this->defaultWidth = $defaultWidth;
        $this->defaultHeight = $defaultHeight;
        
        if (is_array($ttfFileNames)){
            $this->ttfFileNames = $ttfFileNames;
        } else if ($ttfFileNames!==null){
            throw new Exception(
                'Invalid $ttfFileNames',
                500
            );
        } else {
            $ttfPath = realpath(dirname(__FILE__)).'/';
            $this->ttfFileNames = array(
                $ttfPath.'resources/1.ttf',
                $ttfPath.'resources/2.ttf',
                $ttfPath.'resources/3.ttf',
            );
            error_log(var_export($this->ttfFileNames,true));
        }
        
    }
    
}
