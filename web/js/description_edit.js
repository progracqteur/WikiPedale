/**
* This module is used when the user wants to edit a description
*/
define(['jQuery','map_display','descriptions','basic_data_and_functions','json_string','markers_filtering','params'],
        function($,map_display,descriptions,basic_data_and_functions,json_string,markers_filtering,params) {
	var mode_edit = {},
        new_lat = null,
        new_lon = null,
        new_position = null;

    function stop_edition(){
		/**
		* Hides all the forms that were opened (and display the data in text) and
        * stops the postion description edition.
    	*/
    	$("#div_placeDescription div").each(function(i,e) {
        	var id_e = $(e).attr("id");
        	if(id_e != undefined && id_e.indexOf('_edit') != -1 &&  id_e.indexOf('div_') != -1) {
            	$(e).hide();
        	}
    	});

	    $(".ButtonEdit img").each(function(i,e) {
	        $(e).attr("src", basic_data_and_functions.web_dir + "img/edit.png")
            .attr("title", "Editer");
    	    });

    	$("#div_placeDescription span").each(function(i,e) {
    		// show span element except error 
        	var id_e = $(e).attr("id");
        	if(id_e != undefined 
            	&& id_e.indexOf('_error') == -1) {
            	$(e).show();
        	}
    	});

        if (mode_edit['lon_lat']) {
            stop_position_edition();
        }

        mode_edit = {};
	};

    function stop_position_edition() {
        /**
        * Stops the edition of the position in the map
        */
        new_lat = null; //reinit these variables
        new_lon = null;
        new_position = null;
        $("#span_edit_lon_lat_delete_error").hide();
        $('#button_save_lon_lat').hide();
        $('#button_edit_lon_lat').show();
        mode_edit['lon_lat'] = false;

        map_display.undisplay_marker('edit_description');
        map_display.get_map().events.remove("click");

        map_display.redisplay_description_markers();
        markers_filtering.display_only_markers_with_selected_categories();
    }

    function position_edit_or_save() {
        /**
        * When this function is tiggered,
        either the edition mode for position of the selected marker  (map) is displayed
        either the new position of the selected marker is saved
        The choice between is done alternatively
        */
        if (mode_edit['lon_lat'] == undefined || ! mode_edit['lon_lat']) {
            $('#button_save_lon_lat').show();
            $('#button_edit_lon_lat').hide();
            map_display.undisplay_markers();
            map_display.display_marker('edit_description');
            map_display.get_map().events.register("click", map_display.get_map(), function(e) {
                new_position = map_display.get_map().getLonLatFromPixel(e.xy);
                new_lat = new_position.lat;
                new_lon = new_position.lon;

                map_display.marker_change_position('edit_description', new_position);
                map_display.display_marker('edit_description');
            });
            mode_edit['lon_lat'] = true;
        } else {
            if (new_lat !== null) {
                var signalement_id = parseInt($('#input_place_description_id').val());
                var json_request = json_string.edit_place_position(signalement_id,new_lon,new_lat);
                var url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
                $.ajax({
                    type: "POST",
                    data: {entity: json_request},
                    url: url_edit,
                    cache: false,
                    success: function(output_json) { 
                        if(! output_json.query.error) { 
                            var new_description = output_json.results[0];
                            descriptions.single_update(new_description);
                            map_display.marker_change_position(new_description.id, new_position);
                            stop_position_edition(); 
                        } else { 
                            $("#span_edit_lon_lat_delete_error").show();
                        }
                    },
                    error: function(output_json) {
                        $("#span_edit_lon_lat_delete_error").show();
                    }
                });
            }
            else {
                stop_position_edition();
            }            
        }
    }

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
            var url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
        
        	$.ajax({
            	type: "POST",
            	data: {entity: json_request},
            	url: url_edit,
            	cache: false,
            	success: function(output_json) { 
                	if(! output_json.query.error) { 
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
		stop_edition: stop_edition,
	    description_edit_or_save: description_edit_or_save,
        position_edit_or_save:position_edit_or_save,
    };
});
