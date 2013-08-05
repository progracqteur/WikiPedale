var description_creating_form = function () {
	//catchForm
	function process() {
    	/**
    	* Catch the #new_placeForm
    	*/
    	var desc_data = {};
    	var error_messages = "";

    	desc_data['categories'] = new Array();
    	$.map($('#add_new_description_form').serializeArray(), function(n, i){
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
    	    if(!userInAccordWithServer) {
        	        $('#login_message').text("Veuillez vous reconnecter.")
            	    $.colorbox({inline:true, href:"#login_form_div"});
        	} else {
            	if(error_messages != "") {
                	$('#add_new_description_form__message').text('Erreur! ' + error_messages  + 'Merci.');
                	$('#add_new_description_form__message').addClass('errorMessage');
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
                                map_display.delete_marker_for('new_description');
                                clean_form();
                            	if(user.isRegistered()) { //sinon verif de l'email
                                	add_marker_and_description(newPlaceData.geom.coordinates[0],
                                    	newPlaceData.geom.coordinates[1],
                                    	data_map_glue.focus_on_place_of,
                                    	newPlaceData);
	                                $('#add_new_description_form__message').text("Le point noir que vous avez soumis a bien été enregistré. Merci!");
    	                            setTimeout( function(){
                                        data_map_glue.mode_change();
                                        displayPlaceDataFunction(newPlaceData.id);
                                    	},4000);  
                            	} else {
                                	$('#add_new_description_form__message').text("Le point noir que vous avez soumis a bien été enregistré. Avant d'afficher le point noir, nous allons vérifier votre adresse mail. Veuillez suivre les instructions qui vous ont été envoyées par email.");
                                	setTimeout(
                                    	function(){
                                        	data_map_glue.mode_change();
                                    	},4000); 
                            	}
                            	$('#add_new_description_form__message').addClass('successMessage');
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

	// clear_add_new_des
	function clean_form() {
    	/** 
    	* Clear the data entered in the form with id 'add_new_description_form'
    	*/
    	$("#add_new_description_form input[type=text], #add_new_description_form textarea, #add_new_description_form input[type=hidden]").val("");
    	$('#add_new_description_form__message').text("");
    	informer.reset_new_description_form();
	}


	return {
		process: process
    };
}();

