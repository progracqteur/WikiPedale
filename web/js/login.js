/**
* This module contains all the function used when a user want to log in.
*/
define(['jQuery','data_map_glue','user'], function($,data_map_glue,user) {
    function display_login_form_with_message(message){
        /**
        * Display the login form in a colorbox
        * @param {string} the message to be displayeed
        */
        $('#login_message').text(message);
        $.colorbox({inline:true, href:"#login_form_div"});
    }

    function catch_form(){
        /**
        * To be excecuted when the login form is submitted.
        * This function checks asking to the db if couple username/password is correct
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
                ret2 = xhrObj.setRequestHeader("X-WSSE",wsseHeader(user_data['username'], user_data['password']));
            },
            data: "",
            url: url_login,
            cache: false,
            success: function(output_json) { 
                if(! output_json.query.error) { 
                    user.update(output_json.results[0]);
                    update_page_when_logged();
                } else { 
                    $('#login_message').text(output_json[0].message);
                    $('#login_message').addClass('errorMessage');
                }
            },
            error: function(output_json) {
                $('#login_message').text(output_json.responseText);
                $('#login_message').addClass('errorMessage');
            }
        });
    }

    function update_page_when_logged(){
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
        jQuery('.username').text(user.data().label);

        data_map_glue.update_data_and_map();
    }

    return {
        display_login_form_with_message: display_login_form_with_message,
        catch_form: catch_form
    }
});