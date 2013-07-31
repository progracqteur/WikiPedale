var last_description_selected;

function descriptionDelete() {
    /**
    * to delete a description
    */
    signalement_id = parseInt($('#input_place_description_id').val());
    json_request = DeleteDescriptionInJson(signalement_id);
    url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
    $.ajax({
        type: "POST",
        data: {entity: json_request},
        url: url_edit,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                markers_and_associated_data[signalement_id][0].erase();
                markers_and_associated_data[signalement_id] = undefined;
                descriptions.erase(signalement_id)
                $('#div_placeDescription').hide();
                last_description_selected = null;
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


function catchForm(formName) {
    description_creating_form.process();
}

function clear_add_new_description_form() {
    /** 
    * Clear the data entered in the form with id 'add_new_description_form'
    */
    new_placeMarker.display(false);
    description_creating_form.clean();
}

function changingModeFunction() {
    /**
    * Changin the mode between 'add_new_place' and 'edit_place' / 'show_place'.
    */
    if(!add_new_place_mode) {
        $('.olControlButtonAddPlaceItemActive')
            .each(function(index, value){
                value.innerHTML = 'Annuler';
            })
            .removeClass("buttonPlus")
            .addClass("buttonAnnuler");
        map_display.unactivate_markers();

        if(new_placeMarker != undefined) 
            {
                new_placeMarker.display(true);
            }

        map_display.get_map().events.register("click", map_display.get_map(), function(e) {
            informer.map_ok(); //le croix rouge dans le formulaire nouveau point devient verte
            var position = map_display.get_map().getLonLatFromPixel(e.xy);
            $("input[name=lon]").val(position.lon);
            $("input[name=lat]").val(position.lat);

            if(new_placeMarker == undefined) 
                {
                    var size = new OpenLayers.Size(19,25);
                    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
                    var icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name([]) + '_selected.png', size, offset); 
                    new_placeMarker = new OpenLayers.Marker(position,icon);
                    map_display.placesLayer.addMarker(new_placeMarker);
                }
                else 
                {
                    new_placeMarker.display(true);
                    new_placeMarker.lonlat = position;
                    map_display.placesLayer.redraw();
                }
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
            $('.olControlButtonAddPlaceItemActive')
                .each(function(index, value){
                    value.innerHTML = 'Ajouter un signalement';
                })
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");

            if(new_placeMarker != undefined) 
                {
                    new_placeMarker.display(false);
                }

            map_display.get_map().events.remove("click");

            $.each(markers_and_associated_data, function(index, marker_data) {
                if (marker_data != undefined) {
                    marker = marker_data[0];
                    data = marker_data[1];

                    var markerMouseDownFunction = (function(iid)       {
                        return function(evt) {
                            displayPlaceDataFunction(iid);
                            OpenLayers.Event.stop(evt);
                        } }
                    ) (data.id);

                    marker.events.register("mousedown", marker, markerMouseDownFunction);

                    if(last_description_selected != null  && last_description_selected == data.id) {
                        marker.setUrl(marker_img_url + 'm_' + marker_img_name(data.statuses) + '_selected.png');
                        }
                    else {
                        marker.setUrl(marker_img_url + 'm_' + marker_img_name(data.statuses) + '.png');
                    }
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
