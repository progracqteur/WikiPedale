var last_description_selected;

function descriptionDelete() {
    /**
    * to delete a description
    */
    signalement_id = parseInt($('#input_place_description_id').val());
    json_request = json_string.delete_place(signalement_id);
    url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
    $.ajax({
        type: "POST",
        data: {entity: json_request},
        url: url_edit,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                map_display.get_marker_for(signalement_id).erase();
                descriptions.erase(signalement_id);
                $('#div_placeDescription').hide();
                last_description_selected = null;
            }
            else { 
                $('#span_place_description_delete_error').show();
            }
        },
        error: function(output_json) {
            $('#span_place_description_delete_error').show();
        }
    });
}


function catchForm(formName) {
    description_creating_form.process();
}

function clear_add_new_description_form() {
    /** 
    * Clear the data entered in the form with id 'add_new_description_form'
    */
    map_display.undisplay_marker('new_description');
    description_creating_form.clean();
}

function changingModeFunction() {
    /**
    * Changin the mode between 'add_new_place' and 'edit_place' / 'show_place'.
    */
    if(!add_new_place_mode) {
        $('#div_add_new_description_button').text('Annuler')
            .removeClass("buttonPlus")
            .addClass("buttonAnnuler");
        map_display.unactivate_markers();

        map_display.display_marker('new_description');

        map_display.get_map().events.register("click", map_display.get_map(), function(e) {
            informer.map_ok(); //le croix rouge dans le formulaire nouveau point devient verte
            var position = map_display.get_map().getLonLatFromPixel(e.xy);
            $("input[name=lon]").val(position.lon);
            $("input[name=lat]").val(position.lat);

            map_display.marker_change_position('new_description', position);
            map_display.display_marker('new_description');

            });

            if(user.isRegistered()) {
                $("#div_new_place_form_user_mail").hide();
                }
            else {
                $("#div_new_place_form_user_mail").show();
            }
            $("#add_new_description_div").show();
            $("#div_placeDescription").hide();

            add_new_place_mode = true;
        }
        else {
            $('#div_add_new_description_button').text('Ajouter un signalement')
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");

            map_display.undisplay_marker('new_description');

            map_display.get_map().events.remove("click");

            // ne plus utiliser makers_and_assoc_data
            $.each(descriptions.get_all(), function(index, description) {
                marker = map_display.get_marker_for(description.id);

                var markerMouseDownFunction = ( function(iid) {
                    return ( function(evt) {
                            displayPlaceDataFunction(iid);
                            OpenLayers.Event.stop(evt);
                        } )}
                    ) (description.id);

                marker.events.register("mousedown", marker, markerMouseDownFunction);

                if(last_description_selected != null  && last_description_selected == description.id) {
                    map_display.update_marker_for(description.id, 'selected');
                } else {
                    map_display.update_marker_for(description.id, '');
                }
            });

            $("#add_new_description_div").hide();

            if(last_description_selected != null ) {
                $("#div_placeDescription").show();
            }
            add_new_place_mode = false; 
        }
    };

function displayPlaceDataFunction(id_sig) {
    /**
     * Function which display some data of the place on the webpage.
     executed when the user click on a marker on the index page.
     For this page, a marker represents a place
     * @param {OpenLayers.Marker} placeMarker The marker clicked
     * @param {object} placeData The know data given for the place and receivd from 
     web/app_dev.php/place/list/bycity.json?city=mons
     */
    if (last_description_selected) {
        map_display.unselect_marker(last_description_selected);
    }
    map_display.select_marker(id_sig);
    last_description_selected = id_sig;
    description_text_display.display_description_of(id_sig);
}
