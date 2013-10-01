/**
* This module is used when the user want to create a new description (used to catch the 
creating form and to clear this form)
*/
define(['jQuery','basic_data_and_functions','map_display','data_map_glue','informer','user','json_string','descriptions','login'],
        function($, basic_data_and_functions,map_display,data_map_glue,informer,user,json_string,descriptions,login) {
	function catch_creating_form(the_form_to_catch) {
    	/**
    	* Catches the form used to create a new description.
        * @param {DOM elem} the_form_to_catch the DOM elem which is the form to catch.
        * This element must contain a div with an element of class '.message' where to 
        * display the error and success messages.
    	*/
    	var desc_data = {}, 
            error_messages = "",
            messages_div = $(the_form_to_catch).children('.message');

    	desc_data['categories'] = new Array();

    	$.map($(the_form_to_catch).serializeArray(), function(n, i){
        	if (n['name'] == 'categories') { 
            	desc_data['categories'].push(n['value']);
        	} else {
            	desc_data[n['name']] = n['value'];
        	}
    	});

    	if(desc_data['description'] == "") { 
        	error_messages = error_messages + "Veuillez remplir la description. ";
    	}

    	if(desc_data['lieu'] == "") {
        	error_messages = error_messages + "Veuillez indiquer l'adresse. ";
    	}

    	if(desc_data['lon'] == "" || desc_data['lat'] == "") {
        	error_messages = error_messages + "Veuillez indiquer où se trouve le point noir en cliquant sur la carte. ";
    	}

	    if(! user.isRegistered()){
    	    if(desc_data['user_label'] == "") { 
        	    error_messages = error_messages + "Veuillez donner votre nom. ";
         	}

	   	 	if(! basic_data_and_functions.is_mail_valid(desc_data['email'])) {
    	   		error_messages = error_messages + "Veuillez indiquer une adresse email valide. ";
    		}
    	} 	

	    user.isInAccordWithServer().done(function(userInAccordWithServer) {
    	    if(true) {
                    login.display_login_form_with_message('Veuillez vous reconnecter.');
        	} else {
            	if(error_messages != "") {
                	$(messages_div).text('Erreur! ' + error_messages  + 'Merci.');
                	$(messages_div).addClass('errorMessage');
            	} else {
                	entity_string = json_string.edit_place(desc_data['description'], desc_data['lon'],
                    	desc_data['lat'], desc_data['lieu'], desc_data['id'], desc_data['couleur'],
                    	desc_data['user_label'], desc_data['email'], desc_data['user_phonenumber'],desc_data['categories']);
                	url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
                	$.ajax({
                    	type: "POST",
                    	data: {entity: entity_string},
                    	url: url_edit,
                    	cache: false,
                    	success: function(output_json) { 
                        	if(! output_json.query.error) { 
                            	newPlaceData = output_json.results[0];
                                clear_creating_form();
                            	if(user.isRegistered()) { //sinon verif de l'email 
	                                $(messages_div).text("Le point noir que vous avez soumis a bien été enregistré. Merci!");
    	                            setTimeout( function(){
                                        data_map_glue.add_marker_and_description(newPlaceData);
                                        data_map_glue.mode_change();
                                        data_map_glue.focus_on_place_of(newPlaceData.id);
                                        map_display.delete_marker_for('new_description');
                                    	},3000);  
                            	} else {
                                	$(messages_div).text("Le point noir que vous avez soumis a bien été enregistré. Avant d'afficher le point noir, nous allons vérifier votre adresse mail. Veuillez suivre les instructions qui vous ont été envoyées par email.");
                                	setTimeout(
                                    	function(){
                                        	data_map_glue.mode_change();
                                    	},3000); 
                            	}
                                $(messages_div).addClass('successMessage');
                        	} else { 
                            	alert('Mince, il y a un problème. Veuillez nous le signaler. Merci');
                        	}
                    	},
                    	error: function(output_json) {
                        	alert('Mince, il y a un problème. Veuillez nous le signaler. Merci');
                    	}
                	});
            	}
        	}
    	});
	};

	function clear_creating_form(the_form_to_clear) {
    	/** 
    	* Clear the data entered in the form used to create new description.
        * @param {DOM elem} the_form_to_clear the DOM elem which is the form to clear.
    	*/
    	$("#add_new_description_form input[type=text], #add_new_description_form textarea, #add_new_description_form input[type=hidden]").val('');
    	$(the_form_to_clear).children('.message').text('');
    	informer.reset_new_description_form();
	}

	return {
		catch_creating_form: catch_creating_form,
        clear_creating_form: clear_creating_form
    };
});