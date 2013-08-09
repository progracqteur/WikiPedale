var description_text_display = function () {
	var color_trad_text = {};
	color_trad_text['0'] = 'pas encore pris en compte (blanc)';
	color_trad_text['-1'] = 'rejeté (gris)';
	color_trad_text['1'] = 'pris en compte (rouge)';
	color_trad_text['2'] = 'en cours de résolution (orange)';
	color_trad_text['3'] = 'résolu (vert)';

	var current_description_id = null;

	function activate_comments_mode() {
		/**
		* Display description with the 'comments mode'
		*/
        map_display.translate(current_description_id);
    	$("#div_last_private_comment_container").hide();
    	$("#span_plus_de_commenaitres_link").hide();
    	$("#div_list_private_comment_container").show();
    	$("#div_form_commentaires_cem_gestionnaire").show();
    	$("#add_new_description_form__message").val("");
    	scroll(0,0);
	}

	function display_description_of(id_desc) {
    	/**
     	* Function which display some data of the place on the webpage.
     	To be executed when the user click on a marker on the index page.
     	* @param {int} id_desc The id of the description.
     	*/
    	current_description_id = id_desc;
    
	    console.log('utiliser les templates for pop_up_add_photo');
    	photo.refresh_span_photo(id_desc);
    	url_add_photo = "javascript:photo.pop_up_add_photo(" + id_desc + ")";
    	$('a.link_add_photo').each(function() { $(this).attr("href", url_add_photo)});

		var desc_data = descriptions.get_by_id(id_desc);

		categories_list = "";
  		$.each(desc_data.categories, function(i,c) { 
  			categories_list = categories_list + c.label; + " "});

	    $('.class_span_place_description_id').each(function() { this.innerHTML = desc_data.id; });
    	$('.class_span_place_description_loc').each(function() { this.innerHTML = desc_data.addressParts.road; });
    	$('#input_place_description_id').val(desc_data.id);
    	$('#span_place_description_signaleur').text(desc_data.creator.label);
    	$('#span_place_description_loc').text(desc_data.addressParts.road);
    	$('#span_place_description_desc').text(desc_data.description);


    	if(desc_data.moderatorComment != '' || user.isCeM() || user.isAdmin()) {
        	$('#span_place_description_commentaireCeM').text(desc_data.moderatorComment);
        	$('#div_container_place_description_commentaireCeM').show();
    	} else {
        	$('#span_place_description_commentaireCeM').text('');
        	$('#div_container_place_description_commentaireCeM').hide();
    	}

    	$('#span_place_description_cat').text(categories_list);

    	if (desc_data.placetype == null){
        	$('#span_place_description_type').text("pas encore de type assigné");
    	} else {
        	$('#span_place_description_type').text(desc_data.placetype.label);
    	}
    	
    	if (desc_data.manager == null) {
        	$('#span_place_description_gestionnaire').text("pas encore de gestionnaire assigné");
   		} else {
        	$('#span_place_description_gestionnaire').text(desc_data.manager.label);
    	}

    	$('#span_place_description_status').text(color_trad_text[0]);

    	for (i = 0; i < desc_data.statuses.length; i++) {  
            if (desc_data.statuses[i].t == params.manager_color) {
                $('#span_place_description_status').text(color_trad_text[desc_data.statuses[i].v]); 
            }
        }

    	if (user.canVieuwUsersDetails() || user.isAdmin()) {
        	$('#span_place_description_signaleur_contact').html('(email : <a href="mailto:'+ desc_data.creator.email +'">'+ 
        	desc_data.creator.email +'</a>, téléphone : '+ desc_data.creator.phonenumber + ')');
    	}

	    if(user.isGdV() || user.isCeM() || user.isAdmin()) {
    	    comments.update_last(id_desc);
        	comments.update_all(id_desc);
        	$("#span_plus_de_commenaitres_link a").attr("href","javascript:description_text_display.activate_comments_mode()");
        	$("#form_add_new_comment").attr("action","javascript:comments.submit_creation_form(" + desc_data.id + ");");
    	}

    	description_edit_form.hide_forms(); // si l'utilisateur a commencé à éditer , il faut cacher les formulaires
    	display_regarding_to_user_role();

	    $('#div_placeDescription').show();
	}

	function display_editing_button() {
		/**
    	* if the user has certain role, he can edit certain information
    	* this function display or not the button with which we can edit the 
    	* information
    	*/
    	if(user.canModifyCategories() || user.isAdmin()) {
        	$('#span_place_description_cat_button').show();
    	} else {
        	$('#span_place_description_cat_button').hide();
    	}

	    if(user.canModifyLittleDetails() || user.isAdmin()) {
    	    $('#span_place_description_loc_button').show();
        	$('#span_place_description_desc_button').show();
    	} else {
        	$('#span_place_description_loc_button').hide();
        	$('#span_place_description_desc_button').hide();
    	}

	    if(user.canModifyPlacetype() || user.isAdmin()) {
    	    $('#span_place_description_type_button').show();
    	} else {
        $('#span_place_description_type_button').hide();
    	}

	    if(user.canModifyManager() || user.isAdmin()) {
    	    $('#span_place_description_gestionnaire_button').show();
    	} else {
        	$('#span_place_description_gestionnaire_button').hide();
    	}

	    if(user.canUnpublishADescription() || user.isAdmin()){
	        $('#span_place_description_delete_button').show();
	    } else {
	        $('#span_place_description_delete_button').hide();
	    }

	    if(user.isCeM() || user.isAdmin()){
    	    $('#span_place_description_commentaireCeM_button').show();
    	    $('#span_place_description_status_button').show();
    	    $('#div_container_place_description_commentaireCeM').show();
    	} else {
    	    $('#span_place_description_commentaireCeM_button').hide();
    	    $('#span_place_description_status_button').hide();
    	}

	    if(user.isAdmin() || user.isCeM() || user.isGdV()) {
	        $('#div_commentaires_cem_gestionnaire').show();
	    } else{
	        $('#div_commentaires_cem_gestionnaire').hide();
	    }
	}

	function display_regarding_to_user_role() {
		/**
		* The user can have the right to modify some information or to see personnal data
		of the creator.
		*/
		if (user.canVieuwUsersDetails() || user.isAdmin()) {
	    	var desc_data = descriptions.get_by_id(current_description_id);
            $('#span_place_description_signaleur_contact').html('(email : <a href="mailto:'+ desc_data.creator.email +'">'+ 
        	desc_data.creator.email +'</a>, téléphone : '+ desc_data.creator.phonenumber + ')');
        } else {
            $('#span_place_description_signaleur_contact').text('');
        }

        display_editing_button()
	}

	return {
		activate_comments_mode: activate_comments_mode,
		display_description_of: display_description_of,
		display_regarding_to_user_role: display_regarding_to_user_role
    };
}();

