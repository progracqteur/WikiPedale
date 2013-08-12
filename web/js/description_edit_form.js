define(['jQuery','map_display','data_map_glue','descriptions','basic_data_and_functions'],
        function($,map_display,data_map_glue,descriptions,basic_data_and_functions) {
	var mode_edit = {};

	function delete(){
		/**
    	* to delete a description
    	*/
    	json_request = json_string.delete_place(description_id);
    	url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
    	$.ajax({
        	type: "POST",
        	data: {entity: json_request},
        	url: url_edit,
        	cache: false,
        	success: function(output_json) { 
            	if(! output_json.query.error) { 
                    map_display.delete_marker_for(signalement_id);
            		descriptions.erase_id_for_data_relative_to(signalement_id); 
                	$('#div_placeDescription').hide();
                    data_map_glue.last_description_selected_reset();
            }
            else { 
                $('#span_place_description_delete_error').show();
                //console.log('Error else');
                //console.log(JSON.stringify(output_json));
            }
        },
        error: function(output_json) {
            $('#span_place_description_delete_error').show();
            //console.log('Error error');
            //console.log(output_json.responseText);
        }
    });
	}


	function hide_forms(){
		/**
		* Hides all the forms that were opened (and show the data like before
		edition)
    	* dans le div "div_placeDescription", l'utilisateur peut afficher un formulaire pour éditer
    	* certaines données.
    	* si il change de point, il faut afficher le nouveau point dans un mode de non-édition
    	* -> se fait en appeleant cette fonction
    	*/
    	$("#div_placeDescription div").each(function(i,e) {
        	id_e = $(e).attr("id");
        	if(id_e != undefined && id_e.indexOf('_edit') != -1 &&  id_e.indexOf('div_') != -1) {
            	$(e).hide();
        	}
    	});

	    $(".ButtonEdit img").each(function(i,e) {
	        $(e).attr("src", basic_data_and_functions.web_dir + "img/edit.png")
            .attr("title", "Editer");
    	    });

	    mode_edit = {};
       

    	$("#div_placeDescription span").each(function(i,e) {
    		// show span element except error 
        	id_e = $(e).attr("id");
        	if(id_e != undefined 
            	&& id_e.indexOf('_error') == -1) {
            	$(e).show();
        	}
    	});
	};

	function description_edit_or_save(element_type){
		/**
		* When this function is tiggered,
		either the edition form is displayed relative to 'element_type'
		either the data given by the edition form relative 'element_type' is saved
		The choice between the two comportements is in function of the variable 'mode_edit'
		*/
	    element_id = "#span_place_description_" + element_type;
    	signalement_id = parseInt($('#input_place_description_id').val());
        signalement = descriptions.get_by_id(signalement_id);

	    if (mode_edit[element_type] == undefined || ! mode_edit[element_type]) {
	        // SHOW THE EDIT FORM
 	       if (element_type == 'cat'){
    	        categories_selected = Array();
        	    $.each(signalement.categories, function(i,c) { categories_selected.push(c.id); });
        	    $(element_id + '_edit').select2("val", categories_selected);
        	} else if (element_type == 'status') {
            	color_selected = 0;
            	$.each(signalement.statuses, function(i,s) { if(s.t == params.manager_color) color_selected = s.v });
            	$(element_id + '_edit').select2("val", color_selected);
        	} else {
            	$(element_id + '_edit').val($(element_id).text());
        	}

        	$(element_id).hide();
        	$("#div_place_description_" + element_type + '_edit').show();
        	$(element_id + '_button').html(
            	$(document.createElement('img'))
                	.attr("src", basic_data_and_functions.web_dir + "img/sauver.png")
                	.attr("title", "Sauver"));
        	mode_edit[element_type] = true;
    	} else {
        	// SAVE THE FORM
        	if(element_type == "commentaireCeM") {
            	json_request = json_string.edit_moderator_comment(signalement_id,$(element_id + '_edit').val());
        	} else if(element_type == "desc") {
            	json_request = json_string.edit_description(signalement_id,$(element_id + '_edit').val());
        	} else if (element_type == "loc") {
            	json_request = json_string.edit_location(signalement_id,$(element_id + '_edit').val());
        	} else if (element_type == "cat") {
            	json_request = json_string.edit_category(signalement_id,$(element_id + '_edit').select2("val"));
      		} else if (element_type == "status") {
            	json_request = json_string.edit_status(signalement_id,params.manager_color,$(element_id + '_edit').select2("val"));
        	} else if (element_type == "gestionnaire") {
            	json_request = json_string.edit_manager(signalement_id,$(element_id + '_edit').select2("val"));
        	} else if (element_type == "type"){
            	json_request = json_string.edit_place_type(signalement_id,$(element_id + '_edit').select2("val"));
        	}
        	url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
        
        	$.ajax({
            	type: "POST",
            	data: {entity: json_request},
            	url: url_edit,
            	cache: false,
            	success: function(output_json) { 
                	if(! output_json.query.error) { 
                        old_signalement = descriptions.get_by_id(signalement_id);
                        new_description = output_json.results[0];
                    	descriptions.single_update(new_description);
                    	if(element_type == 'cat'){
                            categories_list = "";
                            $.each(new_description.categories, function(i,c) { categories_list = categories_list + c.label; + " "});
                            $(element_id).text(categories_list); 
                        	markers_filtering.display_only_markers_with_selected_categories();
                    	} else if (element_type == 'status'){
                            map_display.update_marker_for(signalement_id, 'selected');
                            markers_filtering.display_only_markers_with_selected_categories();
                	    } else if (element_type == 'gestionnaire') {
                        	$(element_id).text($(element_id + '_edit').select2('data').text);
                    	} else if (element_type == 'type'){
                        	$(element_id).text($(element_id + '_edit').select2('data').text);
                        	markers_filtering.display_only_markers_with_selected_categories();
                    	} else {
                        	$(element_id).text($(element_id + '_edit').val());
                    	}
                    	$(element_id +  '_error').hide();
                    	$("#div_place_description_" + element_type + '_edit').hide();
                    	$(element_id).show();
                    	$(element_id + '_button').html(
                        	$(document.createElement('img'))
                            	.attr("src",  basic_data_and_functions.web_dir + "img/edit.png")
                            	.attr("title", "Editer"));
                    	mode_edit[element_type] = false;
                	} else { 
                    	$(element_id +  '_error').show();
             	    }
            	},
            	error: function(output_json) {
                	$(element_id +  '_error').show();
            	}
        	});
    	};
    	return false;
	};

	return {
		hide_forms: hide_forms,
	    description_edit_or_save: description_edit_or_save,
    };
});
