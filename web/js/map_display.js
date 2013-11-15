/**
* This is for all the action for the display of the map
*/

define(['jQuery','basic_data_and_functions','descriptions','OpenLayers','params'],
        function($,basic_data_and_functions,descriptions,OpenLayers,params) {
    var displaying_tiny_map = false; // Display or not a tiny Map
    var old_center; // To re-center the map after displaying the tiny map

    var map; // Variable to acces to the map
    var osm;
    var placesLayer; // layer where existing places / new place marker are drawn
    var zoom_map = 13; // zoom level of the map



    var marker_img_url = basic_data_and_functions.web_dir + 'js/lib/OpenLayers/img/';
    var markers = [];
    markers['new_description'] = null;

    var size;
    var offset;
    var icon;

    // marker with color
    var color_trad = new Array();
    color_trad['0'] = 'w';
    color_trad['-1'] = 'd';
    color_trad['1'] = 'r';
    color_trad['2'] = 'o';
    color_trad['3'] = 'g';
    
    function map_resizing(){
    	/**
    	* Change the size of the map : tiny map (comment mode) or normal map (normal mode)
    	*/
		if(!displaying_tiny_map) {
            $("#map")
			.width("30%")
			.height("300px");
            $("#ToolsPanel")
			.width("70%");
		} else {
            $("#map")
				.width("50%")
				.height("500px");
           	$("#ToolsPanel")
			.width("50%");
		}
		displaying_tiny_map = ! displaying_tiny_map;
		map.updateSize();
    }

    function translate(current_description_id){
    	/**
    	* Translate the map in the div element for  the tiny map
    	*/
    	old_center = map.getCenter();
		$("#map").hide();
		$("#div_placeDescription").show();
		$("#param_carte").hide();
		$("#olPanelUL").hide();
		$("#map_little").show();
		$("#div_returnNormalMode").show();
		$("#div_dernieres_modifs").hide();
		map.render("map_little");
		map.updateSize();
		if (current_description_id) {
    		map.setCenter(descriptions.get_by_id(current_description_id).lonlat);
    	}
    }

    function untranslate(){
    	/**
    	* Untranslate the map in the div element for  the 'normal map'
    	*/
		$("#div_dernieres_modifs").show();
		$("#div_returnNormalMode").hide();
		$("#map").show();
		$("#div_placeDescription").show();
		$("#param_carte").show();
		$("#olPanelUL").show();
		$("#map_little").hide();
		map.render("map");
		map.updateSize();
    }

    function normal_mode(){
    	/**
    	* Return the map in the normal mode
    	*/
		untranslate();
		$("#div_last_private_comment_container").show();
		$("#span_plus_de_commenaitres_link").show();
		$("#div_list_private_comment_container").hide();
		$("#div_form_commentaires_cem_gestionnaire").hide();
		map.setCenter(old_center);
    }
    
    function init(townLon,townLat){
    	/**
    	* Init the map on the good town
    	*/
		map = new OpenLayers.Map('map', {maxResolution: 1000});
		osm = new OpenLayers.Layer.OSM("OSM MAP");
		map.addLayer(osm);

        var voies_lentes_layer = new OpenLayers.Layer.WMS("Voies Lentes","http://geoservices.wallonie.be/arcgis/services/MOBILITE/VOIES_LENTES/MapServer/WMSServer?",
            {
                layers: '1',
                transparent: true,
            },{
                projection: new OpenLayers.Projection("EPSG:3857"),
            });
        map.addLayer(voies_lentes_layer);

        map.addControl(new OpenLayers.Control.LayerSwitcher());

		map.setCenter(
            new OpenLayers.LonLat(townLon, townLat).transform(
				new OpenLayers.Projection("EPSG:4326"),
				map.getProjectionObject()
            	), zoom_map
            );

		placesLayer = new OpenLayers.Layer.Markers("Uello Markers");
		map.addLayer(placesLayer);

        size = new OpenLayers.Size(19,25);
        offset = new OpenLayers.Pixel(-(size.w/2), -size.h);

        icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name([]) + '_selected.png', size, offset); 
        markers['edit_description'] = new OpenLayers.Marker(new OpenLayers.LonLat(1.1,1.1),icon);
        placesLayer.addMarker(markers['edit_description']);
        markers['edit_description'].display(false);
    }

    function update_marker_for(description_id, option) {
        /**
        * Change the icon of the marker for a description
        * @param {int} description_id The id of the description
        * @param {string} option Some option for the icon ('_selected' or '_no_active' or '' (for normal mode))
        */
        var description_data  = {statuses : []};
        if (description_id !== 'new_description') {
            description_data = descriptions.get_by_id(description_id);
        }
        var end_name = '.png';
        if (option) {
            end_name = '_' + option + '.png';
        }
        markers[description_id].setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + end_name);
    }

    function add_marker(description_id, an_event_function){
        // todo renommer cette fct en add_marker_for_description
        // l126- l129 add_marker(img en option)
    	/**
     	* Add a marker on the  Layer "Existing places" for the description corresponding to the
     	given id. The marker is such that when the user click on it, an action is executed.
     	* @param {integer} description_id The id of the description
     	* @param {function} anEventFunction A function to execute when the user click on the marker
        * (enEventFunction is optinal)
     	*/ 
     	var description_data = descriptions.get_by_id(description_id);

        if(description_data) { //not undefined
            var feature = new OpenLayers.Feature(osm, new OpenLayers.LonLat(description_data.geom.coordinates[0], description_data.geom.coordinates[1]).transform(
                new OpenLayers.Projection("EPSG:4326"),
                map.getProjectionObject()
            ));
        
            icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '.png', size, offset); 
            feature.data.icon = icon;
    
            var marker = feature.createMarker();

            if (an_event_function) {
                var markerMouseDownFunction = function(evt) {
                    an_event_function(description_data.id); 
                    OpenLayers.Event.stop(evt);
                };
            
                marker.events.register("mousedown", marker, markerMouseDownFunction);
                marker.events.register("touchstart", marker, markerMouseDownFunction);
            }
            placesLayer.addMarker(marker);

            markers[description_id] = marker;
        }
    }

    function get_marker_for(description_id){
        /**
        * Get the marker associated to a description
        * @param {int} description_id The id of the description
        */
        return markers[description_id];
    }

    function delete_marker_for(description_id){
        /**
        * Delete the marker associated to a description. This marker
        * can anymore be used or it must recreated by the function add_marker
        * @param {int} description_id The id of the description
        */
        placesLayer.removeMarker(markers[description_id]);
        markers[description_id] = null;
    }

    function unactivate_markers(){
        /**
        * Display all the markers associated to a description as unactivate
        */
        $.each(markers, function(description_id, marker) {
            if (marker != undefined) {
                var description_data = descriptions.get_by_id(description_id);
                marker.events.remove("mousedown");
                marker.events.remove("touchstart");
                marker.setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '_no_active.png')
            }
        });
    }

    function marker_change_position(an_id, new_position) {
        /**
        * Changing the position of a marker.
        * @param {int} an_id The id of the signalement
        * @param {lonlat} new_position The new position
        */
        if((an_id === 'new_description') && markers['new_description'] == null) {
            icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name([]) + '_selected.png', size, offset); 
            markers[an_id] = new OpenLayers.Marker(new_position,icon);
            placesLayer.addMarker(markers[an_id]);
        } else {
            markers[an_id].lonlat = new_position;
            placesLayer.redraw();
        }
    }

    function display_marker(an_id){
        /**
        * Display on the map the marker associate to a description.
        * @param {int} an_id The id of the description
        */
        if (markers[an_id]) {
            markers[an_id].display(true);
            placesLayer.redraw();
        }
    }

    function undisplay_marker(an_id){
        /**
        * Undisplay on the map the marker associate to a description.
        * @param {int} an_id The id of the description
        */
        if (markers[an_id]) {
            markers[an_id].display(false);
            placesLayer.redraw();
        }
    }

    function undisplay_markers(){
        /**
        * Undisplay all the markers associated to a description as unactivate
        */
        $.each(markers, function(description_id, marker) {
            undisplay_marker(description_id);
        });
    }


    function select_marker(an_id){
        /**
        * Sets the marker of a given signalement to 'selected' (in pink)
        * @param {int} an_id The id of the signalement
        */
        var description_data = descriptions.get_by_id(an_id);
        markers[an_id].setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '_selected.png');
        markers['edit_description'].lonlat = markers[an_id].lonlat;
    }   

    function unselect_marker(an_id){
        /**
        * Sets the marker of a given signalement to 'unselected' (in pink)
        * @param {int} an_id The id of the signalement
        */
        var description_data = descriptions.get_by_id(an_id);
        markers[an_id].setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '.png');
    }   

    function display_all_markers(){
        /**
        * Display all the markers on the map.
        */
        $.each(markers, function(description_id, marker) {
            if (marker != undefined) {
                marker.display(true);
            }
        });
    }

    function get_map(){
        /**
        * Get the map.
        */
        return map;
    }

    function marker_img_name(statuses){
        /**
        * Compute the icon name of the marker.
        */
        manager_c = 'w';
        c2 = 'w';
        c3 = 'w';
        for (i = 0; i < (statuses.length); i++)
        {
            if (statuses[i].t == params.manager_color) {
                manager_c = color_trad[statuses[i].v];
            }

            if (params.c2_label != undefined && statuses[i].t == params.c2_label) {
                c2 = color_trad[statuses[i].v];
            }

            if (params.c3_label != undefined && statuses[i].t == params.c3_label) {
                c3 = color_trad[statuses[i].v];
            }
        }

        if (params.c2_label == undefined) {
            return manager_c;
        } else if (params.c3_label == undefined) {
            return manager_c + c2;
        } else {
            return manager_c + c2 + c3;
        }
    }
    
    function reactivate_event_on_description_markers(marker_event, function_on_marker) {
        /**
        * To all the markers related to descriptions, add an event and
        * apply a function it 
        * @param {function : int -> ()} marker_event The event function
        * that take as input the id of the marker
        * @param {function : marker x int  -> ()} function_on_marker The function
        applied to the marker.
        */
        $.each(descriptions.get_all(), function(index, description) {
            marker = get_marker_for(description.id);

            var markerMouseDownFunction = ( function(iid) {
                return ( function(evt) {
                    if (marker_event) {
                        marker_event(iid);
                    }
                    OpenLayers.Event.stop(evt);
                })
            }) (description.id);

            marker.events.register("mousedown", marker, markerMouseDownFunction);
            marker.events.register("touchstart", marker, markerMouseDownFunction);

            function_on_marker(marker,description.id);
        });
        placesLayer.redraw();
    }

    function reactivate_description_markers(marker_event) {
        /**
        * Display the makers related to descriptions (remove the no_active.png
        * of the url of the maker img) Each marker 
        * is associated with an event that is tiggered when the user click on
        * the marker
        * @param {function : int -> ()} marker_event The event function
        * that take as input the id of the marker
        */
        reactivate_event_on_description_markers(marker_event, function(m,i) {
            update_marker_for(i,'');
        });
    }

    function redisplay_description_markers(marker_event) {
        /**
        * Display all the markers related to descriptions. Each marker 
        * is associated with an event that is tiggered when the user click on
        * the marker
        * @param {function : int -> ()} marker_event The event function
        * that take as input the id of the marker
        */
        reactivate_event_on_description_markers(marker_event, function(m,i) {
            m.display('true');
        } );
    }

    return {
    	translate: translate,
    	normal_mode: normal_mode,
    	init: init,
    	get_map: get_map,
    	add_marker: add_marker,
        unactivate_markers: unactivate_markers,
        select_marker: select_marker,
        unselect_marker: unselect_marker,
        display_marker: display_marker,
        undisplay_marker: undisplay_marker,
        undisplay_markers: undisplay_markers,
        display_all_markers: display_all_markers,
        update_marker_for:update_marker_for,
        get_marker_for: get_marker_for,
        marker_change_position: marker_change_position,
        delete_marker_for: delete_marker_for,
        redisplay_description_markers: redisplay_description_markers,
        reactivate_description_markers:reactivate_description_markers
    }
});