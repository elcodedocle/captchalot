<!DOCTYPE html>
<html>

    <!-- 
        @package info\synapp\tools\captcha
        @name captchalot
        @version 0.3.1
        @author Gael Abadin (elcodedocle)
        @license MIT Expat
        @link https://github.com/elcodedocle/captchalot
    -->
    
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="captchalot, captcha" />
        <meta name="description" content="A captcha web service demo web app" />
        <meta name="author" content="Gael Abadin (elcodedocle)" />
        <meta name="copyright" content="&copy; 2014 Gael Abadin (elcodedocle)" />
        <meta name="robot" content="noindex, nofollow" />
        <title>
            <?=
                htmlspecialchars(
                    _(
                        "SynAPPv2's Captchalot - A visual captcha web service (demo web app)"
                    )
                )
            ?>
        </title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <script type='text/javascript' src='//code.jquery.com/jquery-1.11.1.min.js'></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script type='text/javascript' src='captchalot.js'></script>
        <script type='text/javascript'>
            $(document).ready(function(){
                $('#captchalot\\.refreshButton').click();
            });
        </script>
    </head>
    
    <body>
    
        <div class="container">
            
            <h1>
                <?=
                htmlspecialchars(
                    _(
                        "Try Captchalot"
                    )
                )
                ?>
            </h1>
            
            <h2>
                <?=
                htmlspecialchars(
                    _(
                        "A visual captcha RESTful web service"
                    )
                )
                ?>
            </h2>
            
            <form role="form">
                
                <div class="form-group">
    
                    <div id="captchalot.imageContainer">
                        <img
                            src="../resources/placeholder.png"
                            id="captchalot.image"
                            title="visual captcha sequence"
                            alt="captche sequence"
                            />
                    </div>
                    
                    <input
                        type="hidden"
                        name="captchalot.magicWordUuid"
                        id="captchalot.magicWordUuid"
                        />
                    
                    <div class="row">
                        
                        <div class="col-xs-8">
                            
                            <input 
                                type="text"
                                class="form-control"
                                name="captchalot.magicWord" 
                                id="captchalot.magicWord" 
                                placeholder="<?=htmlspecialchars(_("Write here the 2 words you see on the image above"))?>" 
                            />
                            
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="captchalot.validate({
                            uuid:$('#captchalot\\.magicWordUuid').val(),
                            magicword: $('#captchalot\\.magicWord').val(),
                            width: 490,
                            height: 70,
                            callbackSuccess:function(ajaxResponse){
                                console.log(ajaxResponse);
                                window.alert('Captcha OK');
                            }, 
                            callbackError:function(ajaxResponse){
                                console.log(ajaxResponse);
                                window.alert('Invalid Captcha');
                                //set captcha image
                                $('#captchalot\\.image').attr(
                                    'src', 
                                    /* 'data:image/png;base64,' + */ 
                                    ajaxResponse.data['base64CaptchaImage']
                                );
                                //set captcha uuid
                                $('#captchalot\\.magicWordUuid').val(
                                    ajaxResponse.data['captchaId']
                                );
                                //window.alert('there!');
                            }
                        });">
                            validate
                        </button>
                        
                        <button class="btn btn-default" id="captchalot.refreshButton" type="button" onclick="captchalot.validate({
                            uuid:'',
                            magicword:'',
                            width:490,
                            height:70,
                            callbackSuccess:function(ajaxResponse){
                                console.log(ajaxResponse);
                                window.alert('Got a false positive!');
                            }, 
                            callbackError:function(ajaxResponse){
                                console.log(ajaxResponse);
                                //set captcha image
                                $('#captchalot\\.image').attr(
                                    'src', 
                                    /* 'data:image/png;base64,' + */ 
                                    ajaxResponse.data['base64CaptchaImage']
                                );
                                //set captcha uuid
                                $('#captchalot\\.magicWordUuid').val(
                                    ajaxResponse.data['captchaId']
                                );
                                //window.alert('here!');
                            }
                        });">
                            <span class="glyphicon glyphicon-refresh"></span>
                        </button>
                        
                    </div>
                    
                </div>
                
            </form>
            
        </div>
    
    </body>

</html>