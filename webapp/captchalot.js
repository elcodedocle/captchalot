/**
 * Captchalot web application client controller
 * 
 */

var captchalot = {
    
    'validate' : function(opts){

        var XHR = new XMLHttpRequest(),
            responseJSON,
            parameters,
            options = typeof (opts) === 'object'?opts:{
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
