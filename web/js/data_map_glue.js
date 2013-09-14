/**
* This module is the glue between all the other modules. This leads to fundamental function
* for the application.
*/

define(['jQuery','map_display','descriptions','description_text_display','user','informer','json_string'],
        function($,map_display,descriptions,description_text_display,user,informer,json_string) {
    var townId = null;
    var last_description_selected = null;
    var add_new_place_mode = false; // true when the user is in a mode for adding new place

    function init_app(townId_param, townLon, townLat, marker_id_to_display) {
        /**
        * Init the application and the map.
        * @param {int} townId  The id of the town
        * @param {float} townLon The longitude of the town
        * @param {float} townLat The latitude of the town
        * @param {int} marker_id_to_display The id of the marker to display (direct access). It is optional
        * (none if no marker to display)
        */
        townId = townId_param;
        var jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId_param, addUserInfo: true});

        map_display.init(townLon,townLat);

        $(document).ready(function(){
            $('.olControlButtonAddPlaceItemActive')
                .addClass("buttonPlus")
                .each(function(index, value){ value.innerHTML = 'Ajouter un signalement'; });
        });

        $.getJSON(jsonUrlData, function(data) {
            user.update(data.user);
            descriptions.update(data.results, function () {
                $.when(
                    $.each(data.results, function(index, aPlaceData) {
                        map_display.add_marker(aPlaceData.id, focus_on_place_of);
                    })
                ).done( function(){
                    if(marker_id_to_display) {
                        focus_on_place_of(marker_id_to_display);
                    }
                });
            });
        });
    }

    function update_data_and_map(){
        /**
        * Update the data of the app contained in descriptions.js and re-draw the map
        * (regarding to the updated informations)
        */
        descriptions.erase_all();

        jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId});
        $.ajax({
            dataType: "json",
            url: jsonUrlData,
            success: function(data) {
                descriptions.update(data.results,null);
            },
            complete: function() {
                var signalement_id = $('#input_place_description_id').val();
                if (typeof signalement_id !== "undefined" && signalement_id !== "") {
                    // be sure that a place is selected
                    description_text_display.display_regarding_to_user_role();
                }
            }
        });
    }

    function add_marker_and_description(aLon, aLat, anEventFunction, someData) {
        /**
        * Add a marker on the map that when the user click on it, an 
        action is executed.
        * @param {number} aLon The longitude where to add the marker
        * @param {number} aLat The latitude where to add the marker
        * @param {function} anEventFunction A function to execute when the user click on the marker
        * @param {object} someData Some dota passed to the function anEvent
        */
        descriptions.single_update(someData);
        map_display.add_marker(someData.id, anEventFunction);
    }

    function last_description_selected_reset() {
        /**
        * Resetting the private last_description_selected variable.
        */
        last_description_selected = null;
    }
                    

    function last_description_selected_delete() {
        /**
        * Delete a description. The description deleted is the description
        * having its id in the private variable last_description_selected.
        * It is the last displayed description.
        */
        var json_request = json_string.delete_place(last_description_selected);
        var url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
        $.ajax({
            type: "POST",
            data: {entity: json_request},
            url: url_edit,
            cache: false,
            success: function(output_json) { 
                if(! output_json.query.error) { 
                    map_display.get_marker_for(last_description_selected).erase();
                    descriptions.erase(last_description_selected);
                    $('#div_placeDescription').hide();
                    last_description_selected = null;
                } else { 
                    $('#span_place_description_delete_error').show();
                }
            },
            error: function() {
                $('#span_place_description_delete_error').show();
            }
        });
    }

    function mode_change() {
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
            } else {
                $("#div_new_place_form_user_mail").show();
            }
            $("#add_new_description_div").show();
            $("#div_placeDescription").hide();
        }
        else {
            $('#div_add_new_description_button').text('Ajouter un signalement')
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");

            map_display.undisplay_marker('new_description');
            map_display.get_map().events.remove("click");
            map_display.reactivate_description_markers(focus_on_place_of);

            // ne plus utiliser makers_and_assoc_data
            

            $("#add_new_description_div").hide();

            if(last_description_selected !== null ) {
                $("#div_placeDescription").show();
                map_display.select_marker(last_description_selected);
            }
        }
        add_new_place_mode = ! add_new_place_mode;
    }

    function focus_on_place_of(id_sig) {
        /**
        * Function which display some data of the description on the webpage
        * and draw the marker relative to this description as selected.
        * To be executed when the user click on a marker on the global map.
        * @param {int} id_sig The id of the description to display.
        */
        if (last_description_selected) {
            map_display.unselect_marker(last_description_selected);
        }
        map_display.select_marker(id_sig);
        last_description_selected = id_sig;
        description_text_display.display_description_of(id_sig);
    }

    return {
        init_app: init_app,
        update_data_and_map: update_data_and_map,
        add_marker_and_description: add_marker_and_description,
        last_description_selected_reset: last_description_selected_reset,
        last_description_selected_delete: last_description_selected_delete,
        mode_change: mode_change,
        focus_on_place_of: focus_on_place_of,
    }
});
