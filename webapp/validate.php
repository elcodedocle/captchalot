<?php
use \info\synapp\tools\captcha\session;
use \synapp\info\tools\uuid\uuid;
use \info\synapp\tools\captcha\captchaword;
use \info\synapp\tools\captcha\captchaimage;
use \info\synapp\tools\captcha\captcha;

session_start();
ob_start();
$VALIDATION_RESULT_PENDING = 'PENDING';
$VALIDATION_RESULT_OK = 'OK';
$VALIDATION_RESULT_ERROR = 'ERROR';

require_once '../vendor/elcodedocle/uuid/uuid.php';
require_once '../vendor/elcodedocle/cryptosecureprng/cryptosecureprng.php';
require_once '../sessioninterface.php';
require_once '../session.php';
require_once '../captchaword.php';
require_once '../captchaimage.php';
require_once '../captcha.php';

$session = new session(session_id());
$uuidGenerator = new uuid();
$captchaWord = new captchaword();


$options=array('options'=>array('default'=>420, 'min_range'=>70, 'max_range'=>4200));
$width=filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT, $options);
$options=array('options'=>array('default'=>60, 'min_range'=>10, 'max_range'=>600));
$height=filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT, $options);

$captchaImage = new captchaimage($width,$height);
$captcha = new captcha($session,$_SERVER['REMOTE_ADDR'],$uuidGenerator,$captchaWord,$captchaImage);

$output = array(
    'data' => array(
        'validationResult' => $VALIDATION_RESULT_PENDING,
    ),
);

if (
    isset($_REQUEST['uuid'])
    && $_REQUEST['uuid']!==''
    && isset($_REQUEST['magicword'])
    && $_REQUEST['magicword']!==''
){
    if($captcha->validate($_REQUEST['uuid'],$_REQUEST['magicword'])){
        $output['data']['validationResult'] = $VALIDATION_RESULT_OK;
    } else {
        $output['data']['validationResult'] = $VALIDATION_RESULT_ERROR;
    }
}

if($output['data']['validationResult']!==$VALIDATION_RESULT_OK){
    $output['data']=array_merge(
        $output['data'],
        $captcha->getCaptchaUuidAndImgBase64SrcAttrVal()
    );
}

header('Content-type: application/json');
header('Expires: Sun, 1 Jan 2000 12:00:00 GMT');
header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$jsonOutput = json_encode($output);
echo $jsonOutput;

ob_end_flush();