var map;
var projLB = new OpenLayers.Projection("EPSG:31370");
var boundsLB = new OpenLayers.Bounds(38841,18084,300030,170095);

var options = {
    controls: [],
    projection: projLB,
    units: "m",
    maxExtent: boundsLB
	};

function init() {
    map = new OpenLayers.Map('map', options);

    var layer = new OpenLayers.Layer.WMS( "OpenLayers WMS", 
                      "http://vmap0.tiles.osgeo.org/wms/vmap0", 
                      {layers: 'basic'},{
            projection:projLB,
        });
    map.addLayer(layer);

    //osm = new OpenLayers.Layer.OSM("OSM MAP"); -> marche pas pbm de projection
    map.addLayer(osm);
 	    
    var voies_lentes_layer = new OpenLayers.Layer.WMS("Voies Lentes","http://geoservices.wallonie.be/arcgis/services/MOBILITE/VOIES_LENTES/MapServer/WMSServer?",
        {
            layers: '1',
            transparent: true,
        },{
            projection:projLB,
        });
    //console.log(voies_lentes_layer.getURL()); -> voir l'url envoyé au geoserver - pbm? 
    map.addLayer(voies_lentes_layer);

    map.addControl(new OpenLayers.Control.Navigation());
    map.addControl(new OpenLayers.Control.Zoom());
    map.addControl(new OpenLayers.Control.LayerSwitcher());

    map.setCenter(new OpenLayers.LonLat(5,51),1);
    map.zoomToMaxExtent();     
    } 
