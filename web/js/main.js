var map;

var osmLayer; // OSM layer
var placesLayer; // layer where existing places are drawing
var new_placeLayer;  // layer where the user can draw a new place

var add_new_place_mode = false; // true when the user is in a mode for adding new place

var marker_data_array = Array(); // markers and data related to the markers

var zoom_map = 13; // zoom level of the map
var img_url =  '../OpenLayers/img/'  // where is the dir containing the OpenLayers images

var new_placeMarker;

$.ajaxSetup({ cache: false }); // IE save json data in a cache, this line avoids this behavior


/**
 * FUNCTION
 */
function mapWithClickActionMarkers(townId, townLon, townLat, clickAction) {
    /**
     * TODO -> changer le nom
     * @param {townId} TODO
     * @param {townLon} TODO
     * @param {townLat} TODO
     * @param {clickAction} TODO
     */

    jsonUrlData  = '../app_dev.php/place/list/bycity.json?city=' + townId;

    var changingModeFunction = function() {
        if(!add_new_place_mode) {
            $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                value.innerHTML = 'Retour exploration';
            });
            $.each(marker_data_array, function(index, marker_data) {
                if (marker_data != undefined) {
                    marker = marker_data[0];
                    marker.events.remove("mousedown");
                    marker.setUrl(img_url + 'marker-gold.png')
                }
            });

            if(new_placeMarker != undefined) 
                {
                    new_placeMarker.display(true);
                }

            map.events.register("click", map, function(e) {
                var position = map.getLonLatFromPixel(e.xy);
                //document.f.lon.value = position.lon; 
                //document.f.lat.value = position.lat; 

                if(new_placeMarker == undefined) 
                {
                    var size = new OpenLayers.Size(21,25);
                    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
                    var icon = new OpenLayers.Icon( img_url + '/marker-blue.png', size, offset);
                    new_placeMarker = new OpenLayers.Marker(position,icon);
                    placesLayer.addMarker(new_placeMarker);
                }
                else 
                {
                    new_placeMarker.lonlat = position;
                    placesLayer.redraw();
                }
            });

            document.getElementById("div_signaler").style.display = "block";
            document.getElementById("div_placeDetails").style.display = "none";
            add_new_place_mode = true;
        }
        else {
            $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                value.innerHTML = 'Ajouter un point noir';
            });

            if(new_placeMarker != undefined) 
                {
                    new_placeMarker.display(false);
                }

            map.events.remove("click");

            $.each(marker_data_array, function(index, marker_data) {
                if (marker_data != undefined) {
                    marker = marker_data[0];
                    data = marker_data[1];

                    var markerMouseDownFunction = (function(iid)       {
                        return function(evt) {
                            clickAction(marker_data_array[iid][0],marker_data_array[iid][1]);
                            OpenLayers.Event.stop(evt);
                        } }
                    ) (data.id);

                    marker.events.register("mousedown", marker, markerMouseDownFunction);
                    marker.setUrl(img_url + 'marker.png')
                }
            });

            document.getElementById("div_signaler").style.display = "none";
            document.getElementById("div_placeDetails").style.display = "block";
            add_new_place_mode = false; 
        }
    };

    map = new OpenLayers.Map('map');
    osm = new OpenLayers.Layer.OSM("OSM MAP");
    map.addLayer(osm);

    map.setCenter(
        new OpenLayers.LonLat(townLon, townLat).transform(
            new OpenLayers.Projection("EPSG:4326"),
            map.getProjectionObject()
        ), zoom_map );

    placesLayer = new OpenLayers.Layer.Markers("Existing places");
    map.addLayer(placesLayer);

    new_placeLayer = new OpenLayers.Layer.Markers("New place");
    map.addLayer(new_placeLayer);
    new_placeLayer.display(false);
    
    var button_add_place = new OpenLayers.Control.Button({ 
        id : 'buttonAddPlace',
        displayClass: 'olControlButtonAddPlace',
        trigger: changingModeFunction,
        title: 'Button is to be clicked'});

    var control_panel = new OpenLayers.Control.Panel({defaultControl: button_add_place});
    control_panel.addControls([
        button_add_place  ]);
    map.addControl(control_panel);

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive').each(function(index, value){
            value.innerHTML = 'Ajouter un point noir';
        });
    });

    $.getJSON(jsonUrlData, function(data) {
	$.each(data.results, function(index, aPlaceData) {
	    addMarkerWithClickAction(false,
				     aPlaceData.geom.coordinates[0],
				     aPlaceData.geom.coordinates[1],
				     clickAction,
				     aPlaceData); } ) }
	     ); }

function addMarkerWithClickAction(aLayer , aLon, aLat, anEventFunction, someData) {
    /**
     * Add a marker on a layer such that when the user click on it, an 
     action is executed.
     * @param {OpenLayers.Layer} placesLayer  The layer where the marker is added
     * @param {number} aLon The longitude where to add the marker
     * @param {number} aLat The latitude where to add the marker
     * @param {function} anEventFunction A function to execute when the user click on the marker
     * @param {object} someData Some dota passed to the function anEvent
     */
    var feature = new OpenLayers.Feature(osm, new OpenLayers.LonLat(aLon, aLat).transform(
	new OpenLayers.Projection("EPSG:4326"),
	map.getProjectionObject()
    ));
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon(img_url + 'marker.png', size, offset);  
    feature.data.icon = icon;
    
    var marker = feature.createMarker();

    //alert(someData.id);

    marker_data_array[someData.id] = ([marker,someData]);


    var markerMouseDownFunction = function(evt) {
	anEventFunction(marker,someData); 
        OpenLayers.Event.stop(evt);
    };

    marker.events.register("mousedown", marker, markerMouseDownFunction);
    placesLayer.addMarker(marker);
}

function displayPlaceDataFunction(placeMarker, placeData) {
    /**
     * Function which display some data of the place on the webpage.
     executed when the user click on a marker on the index page.
     For this page, a marker represents a place
     * @param {OpenLayers.Marker} placeMarker The marker clicked
     * @param {object} placeData The know data given for the place and receivd from 
     web/app_dev.php/place/list/bycity.json?city=mons
     */
    
    document.getElementById("span_id").innerHTML = placeData.id;
    document.getElementById("span_description").innerHTML = placeData.description;
    document.getElementById("span_nbComm").innerHTML = placeData.nbComm;
    document.getElementById("span_nbVote").innerHTML = placeData.nbVote;
    document.getElementById("span_creator").innerHTML = placeData.creator.label;
    
    document.getElementById("div_placeDetails").style.display = "block";
}



// to be continue
function view_place(place_lon, place_lat) {
    /**
     * Doc TODO
     */
    map = new OpenLayers.Map('map');
    osm = new OpenLayers.Layer.OSM("OSM Map");
    map.addLayer(osm);

    map.setCenter(
        new OpenLayers.LonLat(place_lon, place_lat).transform(
            new OpenLayers.Projection("EPSG:4326"),
            map.getProjectionObject()
        ), zoom_map );

    layer_markers = new OpenLayers.Layer.Markers("Markers");
    map.addLayer(layer_markers);

    var feature = new OpenLayers.Feature(osm, 
         new OpenLayers.LonLat(place_lon, place_lat).transform(
             new OpenLayers.Projection("EPSG:4326"),
             map.getProjectionObject()
        ));
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon(img_url + 'marker.png', size, offset);  
    feature.data.icon = icon;
    
    var marker = feature.createMarker();
    layer_markers.addMarker(marker);
}

