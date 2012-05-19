var map; // contient la carte 
var osm; // contient le layer OSM

var zoom_map = 13; // le niveau de zoom de la carte
var img_url = 'http://localhost:8888/WikiPedale/web/OpenLayers/img/' 

$.ajaxSetup({ cache: false }); // pour IE

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