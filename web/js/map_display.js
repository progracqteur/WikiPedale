/**
* This is for all the action for the display of the map
*/

var map_display = function () {
    var displaying_tiny_map = false; // Display or not a tiny Map
    var old_center; // To re-center the map after displaying the tiny map

    var map; // Variable to acces to the map
    var osm;
    //var osmLayer; // OSM layer
    var placesLayer; // layer where existing places are drawing
    var wplaceLayer;  // layer where the user can draw a new place
    var zoom_map = 13; // zoom level of the map

    var marker_img_url = web_dir + 'OpenLayers/img/';
    var markers = new Array;
    
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

		map.setCenter(
            new OpenLayers.LonLat(townLon, townLat).transform(
				new OpenLayers.Projection("EPSG:4326"),
				map.getProjectionObject()
            	), zoom_map
            );

		placesLayer = new OpenLayers.Layer.Markers("Existing places");
		map.addLayer(placesLayer);
		new_placeLayer = new OpenLayers.Layer.Markers("New place");
		map.addLayer(new_placeLayer);
		new_placeLayer.display(false);
    }

    function add_marker(description_id, an_event_function){
    	/**
     	* Add a marker on the  Layer "Existing places" for the description corresponding to the
     	given id. The marker is such that when the user click on it, an action is executed.
     	* @param {integer} description_id The id of the description
     	* @param {function} anEventFunction A function to execute when the user click on the marker
     	*/ 
     	var description_data = descriptions.get_by_id(description_id);

        if(description_data) { //not undefined
            var feature = new OpenLayers.Feature(osm, new OpenLayers.LonLat(description_data.geom.coordinates[0], description_data.geom.coordinates[1]).transform(
                new OpenLayers.Projection("EPSG:4326"),
                map.getProjectionObject()
            ));
        
            var size = new OpenLayers.Size(19,25);
            var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
            var icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '.png', size, offset); 
            feature.data.icon = icon;
    
            var marker = feature.createMarker();

            var markerMouseDownFunction = function(evt) {
                an_event_function(description_data.id); 
                OpenLayers.Event.stop(evt);
            };

            marker.events.register("mousedown", marker, markerMouseDownFunction);
            placesLayer.addMarker(marker);

            markers[description_id] = marker;
        }
    }

    function get_marker_for(description_id){
        return markers[description_id];
    }

    function unactivate_markers(){
        $.each(markers, function(description_id, marker) {
            if (marker != undefined) {
                var description_data = descriptions.get_by_id(description_id);
                marker.events.remove("mousedown");
                marker.setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '_no_active.png')
            }
        });
    }

    function display_marker(an_id){
        markers[an_id].display(true);
    }

    function undisplay_marker(an_id){
        markers[an_id].display(false);
    }

    function select_marker(an_id){
        /**
        * Sets the marker of a given signalement to 'selected' (in pink)
        * @param {int} an_id The id of the signalement
        */
        var description_data = descriptions.get_by_id(an_id);
        markers[an_id].setUrl(marker_img_url + 'm_' + marker_img_name(description_data.statuses) + '_selected.png');
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
        * display all the markers on the map
        */
        $.each(markers, function(description_id, marker) {
            if (marker != undefined) {
                marker.display(true);
            }
        });
    }

    function get_map(){
        return map;
    }

    return {
    	translate: translate,
    	normal_mode: normal_mode,
    	init: init,
    	get_map: get_map,
    	map_display: map_display,
    	add_marker: add_marker,
        unactivate_markers: unactivate_markers,
        select_marker: select_marker,
        unselect_marker: unselect_marker
    }
}();
