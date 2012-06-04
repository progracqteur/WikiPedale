var map; // contient la carte 
var osm; // contient le layer OSM

var zoom_map = 13; // le niveau de zoom de la carte
var img_url =  '../OpenLayers/img/' // http://localhost:8888/WikiPedale/web/OpenLayers/img/

$.ajaxSetup({ cache: false }); // pour IE

function add_marker(markers,f_event, x, y) {
    // TODO passer tous les elements en un seul object genre d
    // et non x, y et ... (voir orangeade)
    var feature = new OpenLayers.Feature(osm, 
         new OpenLayers.LonLat(x, y).transform(
             new OpenLayers.Projection("EPSG:4326"),
             map.getProjectionObject()
        ));
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon(img_url + 'marker.png', size, offset);  
    feature.data.icon = icon;
    
    var marker = feature.createMarker();
    markers.addMarker(marker);

    var markerClick = function(evt) {
	alert("blop : " + x + " " + y);
        OpenLayers.Event.stop(evt);
    };
    marker.events.register("mousedown", feature, markerClick);
    markers.addMarker(marker);
}

function map_with_action(ville, lon_ville, lat_ville, action) {
    url_data  = '../app_dev.php/place/list/bycity.json?city=' + ville;
    map = new OpenLayers.Map('map');
    osm = new OpenLayers.Layer.OSM("OSM MAP");
    map.addLayer(osm);
    map.setCenter(
        new OpenLayers.LonLat(lon_ville, lat_ville).transform(
            new OpenLayers.Projection("EPSG:4326"),
            map.getProjectionObject()
        ), zoom_map );
      
    var markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);

    $.getJSON(url_data, function(data) {
	$.each(data.results, function(i,row_db) {
	    add_marker(markers, action, row_db.geom.coordinates[0], row_db.geom.coordinates[1]); 
	    })
	});
}


function view_place(place_lon, place_lat) {
    map = new OpenLayers.Map('map');
    osm = new OpenLayers.Layer.OSM("OSM Map");
    map.addLayer(osm);

    map.setCenter(
        new OpenLayers.LonLat(place_lon, place_lat).transform(
            new OpenLayers.Projection("EPSG:4326"),
            map.getProjectionObject()
        ), zoom_map );

    var markers = new OpenLayers.Layer.Markers("Markers");
    map.addLayer(markers);

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
    markers.addMarker(marker);
}

