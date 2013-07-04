function catchLoginForm(){
    /**
    * When the login form is throwed.
    * Asks to the db if couple username/password is correct
    */
    var user_data = {};
    $.map($('#loginForm').serializeArray(), function(n, i){
        user_data[n['name']] = n['value'];
    });

    url_login = Routing.generate('wikipedale_authenticate', {_format: 'json'});
    $.ajax({
        type: "POST",
        beforeSend: function(xhrObj){
            ret = xhrObj.setRequestHeader("Authorization",'WSSE profile="UsernameToken"');
            //console.log(ret);
            ret2 = xhrObj.setRequestHeader("X-WSSE",wsseHeader(user_data['username'], user_data['password']));
            //console.log(ret);
        },
        data: "",
        url: url_login,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                //console.log("catchLoginForm - output success" + JSON.stringify(output_json.results[0]));
                updateUserInfo(output_json.results[0]);
                updatePageWhenLogged();
            }
            else { 
                $('#login_message').text(output_json[0].message);
                $('#login_message').addClass('errorMessage');
                }
        },
        error: function(output_json) {
            //console.log(JSON.stringify(output_json));
            $('#login_message').text(output_json.responseText);
            $('#login_message').addClass('errorMessage');
        }
    });
}

function updatePageWhenLogged(){
    /**
    * Updates the menu when the user is logged :
    * - connexion link and register link : disappear
    * - user name and logout link : appear
    */
    $("#menu_user_name").css('display', 'inline-block');
    $("#menu_connexion").hide();
    $("#menu_logout").css('display', 'inline-block');
    $("#menu_register").hide();

    $("#div_new_place_form_user_mail").hide();

    jQuery('a.connexion').colorbox.close('');
    jQuery('.username').text(user.label);

    update_markers_and_associated_data();
}