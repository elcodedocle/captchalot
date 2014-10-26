captchalot
==========

#####*A dictionary/symbol based captcha generator and validator RESTful service*

 Copyright (C) 2014 Gael Abadin<br/>
 License: [MIT Expat][1]<br />
 [![Code Climate](https://codeclimate.com/github/elcodedocle/captchalot.png)](https://codeclimate.com/github/elcodedocle/captchalot)
 
![captchalot captcha generator test site snapshot with default settings](http://i.imgur.com/Um1jEpp.png "This is how captchalot's test web app looks like. Check it out on https://synapp.info/captchalot ;-) )")

 
### Motivation

I wanted to implement a simple, easy to use, scalable and independent RESTful captcha service on my web app. 

### Requirements

 * PHP >= 5.3 with PDO support
 * MySQL / MariaDB

### Deployment

 * You can install and deploy captchalot using composer:
 
```bash
php composer.phar create-project -s "beta" elcodedocle/captchalot
```

 * You need to edit `config.php.dist` in order to provide basic database connection parameters; then save it as `config.php`. 

 * Here is a basic client written in javascript to AJAX request/refresh/validate a captcha:

```javascript
var captchalot = {
    
    'validate' : function(opts){

        var XHR = new XMLHttpRequest(),
            responseJSON,
            parameters,
            options = (typeof (opts) === 'object')?opts:{
                uuid: '',
                magicword: '',
                width: 350,
                height: 50,
                callbackSuccess: function(responseJSON){ alert('success!'); console.log(responseJSON); },
                callbackError: function(responseJSON){ alert('error!'); console.log(responseJSON); }
            };
        
        XHR.addEventListener("load", function(event) {
            
            try {
                responseJSON = JSON.parse(event.target.responseText);
                if (
                    !('data' in responseJSON) ||
                    !('validationResult' in responseJSON.data) ||
                        responseJSON.data['validationResult'] !== 'ERROR'
                          && responseJSON.data['validationResult'] !== 'PENDING'
                            && responseJSON.data['validationResult'] !== 'OK'
                ){
                    console.log(
                        "Cannot understand response from server:\n"
                            + responseJSON
                    );
                    options.callbackError(responseJSON);
                } else {
                    if ( responseJSON.data['validationResult'] === 'OK' ){
                        options.callbackSuccess(responseJSON);
                    } else {
                        options.callbackError(responseJSON);
                    }
                }
            } catch (e) {
                console.error("Parsing error:", e);
                console.log(event.target.responseText);
            }
            
        });
        
        XHR.addEventListener("error", function(event) {
            alert('Something went wrong ¯\(º_o)/¯');
            console.log(event.target.responseText);
        });
        
        XHR.open("POST", 'validate.php', true);
        XHR.setRequestHeader(
            'Content-type', 
            'application/x-www-form-urlencoded'
        );
        parameters = 'uuid=' 
            + options.uuid 
            + '&magicword=' 
            + encodeURIComponent(options.magicword)
            + '&width='+options.width
            + '&height='+options.height;
        XHR.send(parameters);
        
    }
    
};
```

 * And here is the server-side PHP controller for this client:
 
```php
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
```

 * Check the code (or generate the docs using phpdocumentor) if you want more info on tweaks and supported config/input parameters and values.

### Web app

You can test the code above on the provided web app ([validate.php][2], [captchalot.js][3] and [index.php][4]) you can test 
by giving those files public execution/access permissions on your web server, then pointing your browser to `index.php`

Here is the demo: https://synapp.info/captchalot

### Service pitfalls 

The web app was designed in the simplest possible way for embedding on a couple of quite low load services that very seldom require captcha actions. That means it wasn't designed with efficiency or performance in mind, although scalability was considered (if your server starts to choke just move the captcha service to AWS or something like that and start throwing on-demand instances at the problem ;-)).

### Acks

Some anonymous internet citizen for posting a blog entry years ago (which I wasn't able to find back) showing how to use imagepng for creating an image and drawing lines and adding text to it
[Peter Norvig](http://norvig.com/), publisher of the [compilation of the 1/3 million most frequent English words](http://norvig.com/ngrams/count_1w.txt) on the [natural language corpus data ](http://norvig.com/ngrams/) from where the [word list](https://github.com/elcodedocle/captchalot/blob/master/top10000.php) used by the default dictionary source for this project has been derived.

And that's all for now, folks. If you like this project, feel free to buy me a beer ;-)

bitcoin: 1G4d1Ak4aRXqN8SFqiiiGMFSaffxFbu5EX 

dogecoin: D8axrRWBZA686kEey1rCXXXamjGg9f6A6s 

paypal: http://goo.gl/Q2kRFG


Have fun.-

[1]: https://raw.githubusercontent.com/elcodedocle/captchalot/master/LICENSE
[2]: https://github.com/elcodedocle/captchalot/blob/master/webapp/validate.php
[3]: https://github.com/elcodedocle/captchalot/blob/master/webapp/captchalot.js
[4]: https://github.com/elcodedocle/captchalot/blob/master/webapp/index.php
