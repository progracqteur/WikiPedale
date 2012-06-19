/**
 * PARAMETERS
 */
var map; // will contain the openlayers map
var osmLayer;
var places_with_clic_layer;
var places_no_clic_layer;
var add_new_place_layer;
var add_new_place_mode = false;

var zoom_map = 13; // zoom level of the map
var img_url =  '../OpenLayers/img/'  // where is the dir containing the OpenLayers images

var input_marker = null;

$.ajaxSetup({ cache: false }); // IE save json data in a cache, this line avoids this behavior


/**
 * FUNCTION
 */

function mapWithClickActionMarkers(townId, townLon, townLat, clickAction) {
    /**
     * Create a map with markers. When the user click on a marker,
     an action is executed.
     * @param {string} townId Identifier of the town
     * @param {number} townLon Longitude of the town
     * @param {number} townLat Latitude of the town
     * @param {function} clickAction Action executed when the user click on the
     marker
     */
    jsonUrlData  = '../app_dev.php/place/list/bycity.json?city=' + townId;

    //map = new OpenLayers.Map('map');

    /*
    var zoom_panel = new OpenLayers.Control.Panel();
    zoom_panel.addControls([
        new OpenLayers.Control.ZoomIn(),
        new OpenLayers.Control.ZoomOut()
        //new OpenLayers.Control.Button({ displayClass: "MyButton", trigger: alert('blop') })
    ]);
    */

    //map.addControl(zoom_panel);

    // new OpenLayers.Control.ScaleLine() échelle en bas de la carte
    // new OpenLayers.Control.Navigation() sert pour double clic zoom scroll in -> zoom in

    var blop_func = function() {
        if(! add_new_place_mode)
        {
                    $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                        value.innerHTML = 'Retour vision';
                    });
                    document.getElementById("div_signaler").style.display = "block";
                    places_with_clic_layer.display(false);
                    places_no_clic_layer.display(true);
                    add_new_place_layer.display(true);
            
                    map.events.register("click", map, function(e) {
                    var position = map.getLonLatFromPixel(e.xy);
            
                    if(input_marker == null) 
                    {
                        var size = new OpenLayers.Size(21,25);
                        var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
                        var icon = new OpenLayers.Icon(img_url  +'marker_white.png', size, offset);
                        input_marker = new OpenLayers.Marker(position,icon);
                        add_new_place_layer.addMarker(input_marker);
                    }
                    else 
                    {
                        var position = map.getLonLatFromPixel(e.xy);
                        input_marker.lonlat = position;
                        add_new_place_layer.redraw();
                    }
                    
        });
        add_new_place_mode = true;
        }
        else
        {
            $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                value.innerHTML = 'Ajouter un point noir';
            });

            places_with_clic_layer.display(true);
            places_no_clic_layer.display(false);
            add_new_place_layer.display(false);
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
    
    add_new_place_layer = new OpenLayers.Layer.Markers("Add new place");
    map.addLayer(add_new_place_layer);

    places_with_clic_layer = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(places_with_clic_layer);

    places_no_clic_layer = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(places_no_clic_layer);

    /*
    button = new OpenLayers.Control.Button ({displayClass: 'olControlButtonAddPlace', trigger: blop_func, title: 'Button is to be clicked'});
    panel = new OpenLayers.Control.Panel({defaultControl: button});
    panel.addControls([button]);
    map.addControl (panel);
    */



    
    var button_add_place = new OpenLayers.Control.Button({ 
        id : 'buttonAddPlace',
        displayClass: 'olControlButtonAddPlace',
        trigger: blop_func,
        title: 'Button is to be clicked'});
    var control_panel = new OpenLayers.Control.Panel({defaultControl: button_add_place});
    control_panel.addControls([
        button_add_place  ]);
    map.addControl(control_panel);

    //document.getElementByClass('buttonAddPlace').innerHTML = 'Ajouter un point';

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
     * @param {OpenLayers.Layer} places_with_clic_layer  The layer where the marker is added
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

    var markerMouseDownFunction = function(evt) {
	anEventFunction(marker,someData); 
        OpenLayers.Event.stop(evt);
    };
    marker.events.register("mousedown", feature, markerMouseDownFunction);
    places_with_clic_layer.addMarker(marker);

    var feature = new OpenLayers.Feature(osm, new OpenLayers.LonLat(aLon, aLat).transform(
    new OpenLayers.Projection("EPSG:4326"),
    map.getProjectionObject()
    ));
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon(img_url + 'marker.png', size, offset);  
    feature.data.icon = icon;
    
    var marker = feature.createMarker();

    places_no_clic_layer.addMarker(marker);
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

