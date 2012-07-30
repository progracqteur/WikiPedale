var map;
var osmLayer; // OSM layer
var placesLayer; // layer where existing places are drawing
var new_placeLayer;  // layer where the user can draw a new place
var zoom_map = 13; // zoom level of the map

var img_url =  '../OpenLayers/img/';  // where is the dir containing the OpenLayers images

var add_new_place_mode = false; // true when the user is in a mode for adding new place
var markers_and_associated_data = Array(); // all the markers drawed on the map and the associated data


var path_root = './';

var new_placeMarker;

$.ajaxSetup({ cache: false }); // IE save json data in a cache, this line avoids this behavior


/* <- USER */
var isAdmin = false; // true si l'utilisateur est admin
/* USER -> */

var blopFunction = function() {
        alert('blop');
    }


function jsonForSavingPlace(description, lon, lat, address, id) {
    /* DOIT AUSSI MARCHER POUR EDITION */
    /**
    * Returns a json string used for adding a new place
    * @param {string} description The description of the new place
    * @param {string} lon The longitude of the new place
    * @param {string} lat The latitude of the new place
    * @param {string} address The address of the new place
    */
    if(id==undefined || id==null){
        ret = '{"id":null';
    }
    else{
        ret = '{"id":' + JSON.stringify(id) 
    }
    
    p = new OpenLayers.Geometry.Point(lon, lat);
    p.transform(map.getProjectionObject(), new OpenLayers.Projection('EPSG:4326'));
    parser = new OpenLayers.Format.GeoJSON();
    jsonp = parser.write(p, false);
    alert(jsonp);
    
    return ret + ',"description":' + JSON.stringify(description) 
        +',"creator":{"id":null,"label":' + JSON.stringify("arecupererdansleformlaire") +',"entity":"user"' +'}'
        + "," +'"geom":'+ jsonp 
        + ',"addressParts":{"road":' +
            JSON.stringify(address) 
            + ',"entity":"address"},"entity":"place"}';
}

function catchPlaceForm(formName) {
    /* DOIT AUSSI MARCHER POUR EDITION -> donner le num
    formulaire en option? */
    /**
    * saving the data from the new_placeForm in the db
    */
    var place_data = {};
    alert($(formName).serialize());
    $.map($("#new_placeForm").serializeArray(), function(n, i){
        place_data[n['name']] = n['value'];
    });

    if(
        place_data['description'] == "" || 
        (! place_data['lon'] == undefined && place_data['lon'] == "") ||
        (! place_data['lat'] == undefined && place_data['lat'] == "") ||
        place_data['lieu'] == "") {
        alert('Veuillez remplir entièrement le formulaire. Merci.');
    }
    else {
        alert(jsonForSavingPlace(place_data['description'], place_data['lon'],
            place_data['lat'], place_data['lieu'], place_data['id']))
        entitystring = jsonForSavingPlace(place_data['description'], place_data['lon'],
            place_data['lat'], place_data['lieu'], place_data['id']);
        $.ajax({
            type: "POST",
            data: {entity: entitystring},
            url: path_root + "place/change.json",
            cache: false,
            success: function(output_json) { 
                var output = jQuery.parseJSON(output_json);
                alert(output_json);
                alert('merci');
            },
            error: function(output) {
                alert(output);
                alert("erreur");
            }
        });

    }
}


function homepageMap(townId, townLon, townLat, clickAction) {
    /**
     * TODO -> changer le nom et voir pour la gestion ce qui peut etre reutiliser
     * @param {townId} TODO
     * @param {townLon} TODO
     * @param {townLat} TODO
     * @param {clickAction} TODO
     */

    jsonUrlData  =  path_root + 'place/list/bycity.json?city=' + townId;

    

    var changingModeFunction = function() {
        if(!add_new_place_mode) {
            $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                value.innerHTML = 'Retour exploration';
            });
            $.each(markers_and_associated_data, function(index, marker_data) {
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
                $("input[name=lon]").val(position.lon);
                $("input[name=lat]").val(position.lat);

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
            document.getElementById("div_placeEdit").style.display = "none";
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

            $.each(markers_and_associated_data, function(index, marker_data) {
                if (marker_data != undefined) {
                    marker = marker_data[0];
                    data = marker_data[1];

                    var markerMouseDownFunction = (function(iid)       {
                        return function(evt) {
                            alert('blop');
                            alert(markers_and_associated_data[iid][1]);
                            alert(markers_and_associated_data[iid][0]);
                            clickAction(markers_and_associated_data[iid][0],markers_and_associated_data[iid][1]);
                            OpenLayers.Event.stop(evt);
                        } }
                    ) (data.id);

                    marker.events.register("mousedown", marker, markerMouseDownFunction);
                    marker.setUrl(img_url + 'marker.png')
                }
            });

            document.getElementById("div_signaler").style.display = "none";
            if(isAdmin) { document.getElementById("div_placeEdit").style.display = "block"; }
            else { document.getElementById("div_placeDetails").style.display = "block"; }
            
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

    var button_lately_added = new OpenLayers.Control.Button({ 
        id : 'buttonLatelyAdded',
        displayClass: 'olControlButtonLatelyAdded',
        trigger: blopFunction,
        title: 'Button is to be clicked'});

    var button_lately_updated = new OpenLayers.Control.Button({ 
        id : 'buttonLatelyUpdated',
        displayClass: 'olControlButtonLatelyUpdated',
        trigger: blopFunction,
        title: 'Button is to be clicked'});

    var control_panel = new OpenLayers.Control.Panel({
        div: document.getElementById('olPanelUL')});
    map.addControl(control_panel);
    control_panel.addControls([button_lately_added, button_lately_updated, button_add_place ]);
    
    button_add_place.activate();
    button_lately_added.activate();
    button_lately_updated .activate();

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive').each(function(index, value){
            value.innerHTML = 'Ajouter un point noir';
        });
    });
    $(document).ready(function(){
        $('.olControlButtonLatelyAddedItemActive').each(function(index, value){
            value.innerHTML = 'Derniers ajoutés';
        });
    });
    $(document).ready(function(){
        $('.olControlButtonLatelyUpdatedItemActive').each(function(index, value){
            value.innerHTML = 'Derniers modifiés';
        });
    });
    $.getJSON(jsonUrlData, function(data) {
	$.each(data.results, function(index, aPlaceData) {
        //alert(aPlaceData)
	    addMarkerWithClickAction(false,
				     aPlaceData.geom.coordinates[0],
				     aPlaceData.geom.coordinates[1],
				     clickAction,
				     aPlaceData); } ) }
	     );
}

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

    markers_and_associated_data[someData.id] = ([marker,someData]);


    var markerMouseDownFunction = function(evt) {
	anEventFunction(marker,someData); 
        OpenLayers.Event.stop(evt);
    };

    marker.events.register("mousedown", marker, markerMouseDownFunction);
    placesLayer.addMarker(marker);
}

display_placeEdit_vars = [
    ['.span_id', 'id'],
    ['.span_nbComm', 'nbComm'],
    ['.span_nbVote', 'nbVote'],
]

function displayPlaceDataFunction(placeMarker, placeData) {
    /**
     * Function which display some data of the place on the webpage.
     executed when the user click on a marker on the index page.
     For this page, a marker represents a place
     * @param {OpenLayers.Marker} placeMarker The marker clicked
     * @param {object} placeData The know data given for the place and receivd from 
     web/app_dev.php/place/list/bycity.json?city=mons
     */
    $('.span_id').each(function() { this.innerHTML = placeData.id; });
    $('.span_nbComm').each(function() { this.innerHTML = placeData.nbComm; });
    $('.span_nbVote').each(function() { this.innerHTML = placeData.nbVote; });
    $('.span_creator').each(function() { this.innerHTML = placeData.creator.label; });
    
    if (isAdmin) {
        document.getElementById("f_description").value = placeData.description;
        document.getElementById("div_placeEdit").style.display = "block";
    }
    else {
        document.getElementById("span_description").innerHTML = placeData.description;
        document.getElementById("div_placeDetails").style.display = "block";
    }
}

