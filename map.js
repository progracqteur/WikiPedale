var map;
var osm;
var marker_layer;
var input_marker;

var zoom_map = 13;

var markeur_selectionne = null;
var ancienne_couleur;


var photo_height_details = 150;
var marker_onload_select = null;
var url_data_updated;

$.ajaxSetup({ cache: false });

// PHOTO
function pop_up_add_photo(i, photo_height) {
	pop_up = window.open('photo_add.php?id=' + i);
}

function rafraichir_photo_in(id,div_photo,photo_height) {
    $.getJSON('photo_list.php?id=' + id, function(data) {
	if(data.length == 0) {
	    div_photo.innerHTML = 'pas encore de photos.';
	    }
	else {
		div_photo.innerHTML = '<br />';
	    $.each(data, function(i,row) {
		div_photo.innerHTML +=  ' <a target="_blank" href="' + row.url_image + '"><image height="' + photo_height  + '" src="' + row.url_miniature + '"></image></a>';
	    })
		}
	});
}

function rafraichir_photo(id, photo_height) {
    div_photo = document.getElementById("span_photo");
	rafraichir_photo_in(id,div_photo,photo_height);
}

function rafraichir_photo_opener(photo_height) {
	div_photo = window.opener.document.getElementById("span_photo");
	id_photo = window.opener.document.getElementById("span_id").innerHTML;
	rafraichir_photo_in(id_photo,div_photo,photo_height);
}


// FORMULAIRE EDITION
function save_formulaire_edition(i) {
    $.ajax({
        type: "POST",
        data: $("#formulaire_edition").serialize(),
        url: "saving_edit.php",
		cache: false,
        success: function(output_json) { 
			var output = jQuery.parseJSON(output_json);
			if(output.ok) {
				document.getElementById("message_erreur").style.display = "none";
				update_event_marker(markeur_selectionne,i);
			}
			else {
				if(output.id_erreur == 1) {
					document.getElementById("message_erreur").style.display = "block";
				}
				else {
					alert("Erreur (" + output.id_erreur + "), contactez le webmaster.");
				}
			}
		},
        error: function(output) {
            alert("Erreur, contactez le webmaster.");
        }
	});
}

function details_point_noir(marker,data) {
	document.getElementById("span_id").innerHTML = data.id;
	document.getElementById("f_lieu").innerHTML = data.lieu;
    document.getElementById("f_description").innerHTML = data.description;

	document.getElementById("link_add_photo").href="javascript:pop_up_add_photo(" + data.id + ")";
    
	document.getElementById("div_details_point_noir").style.display = "block";

    rafraichir_photo(data.id,photo_height_details);

    if(markeur_selectionne != null) {
	markeur_selectionne.setUrl('../img/marker_' + ancienne_couleur + '_test.png');
    }

    ancienne_couleur = data.couleur;
    markeur_selectionne = marker;

    marker.setUrl('../img/marker_' + ancienne_couleur + '_select.png');	
}

function edition_points_noirs(marker,data) {
    document.getElementById("span_id").innerHTML = data.id;
    document.getElementById("span_nomprenom").innerHTML = data.nom_prenom;
    document.getElementById("span_email").innerHTML = '<a href="mailto:' +  data.email + '">' +  data.email + '</a>';
	document.getElementById("f_id").value = data.id;
	document.getElementById("f_lieu").value = data.lieu;
    document.getElementById("f_description").value = data.description;
    document.getElementById(data.couleur).selected = true;
    document.getElementById("div_accueil").style.display = "none";
    document.getElementById("div_edition").style.display = "block";

    document.f.action="javascript:save_formulaire_edition(" + data.id + ")";
	document.getElementById("link_add_photo").href="javascript:pop_up_add_photo(" + data.id + ")";
    document.getElementById("link_delete").href="delete.php?id=" + data.id;
    

    rafraichir_photo(data.id,photo_height_details);

    if(markeur_selectionne != null) {
	markeur_selectionne.setUrl('../img/marker_' + ancienne_couleur + '_test.png');
    }

    ancienne_couleur = data.couleur;
    markeur_selectionne = marker;

    marker.setUrl('../img/marker_' + ancienne_couleur + '_select.png');
};

function update_event_marker(marker, id) {
    marker.events.remove("mousedown");
	$.getJSON(url_data_updated + '&id=' + id, function(data) {
	$.each(data, function(i,row_db) {
		var markerClick = function(evt) {
			edition_points_noirs(marker,row_db);
			OpenLayers.Event.stop(evt);
	    };
	    marker.events.register("mousedown", marker.icon, markerClick);
	
		ancienne_couleur = row_db.couleur;
		marker.setUrl('../img/marker_' + ancienne_couleur + '_select.png');
		
	    })
	});	  
}
 

function add_marker(markers,f_event, d) {
    var feature = new OpenLayers.Feature(osm, new OpenLayers.LonLat(d.lon,d.lat));


    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon('../img/marker_' + d.couleur + '_test.png', size, offset);  
    feature.data.icon = icon;
    
    var marker = feature.createMarker();
    marker.id = d.id;

    var markerClick = function(evt) {
		f_event(marker,d);
        OpenLayers.Event.stop(evt);
    };
    marker.events.register("mousedown", feature, markerClick);
    markers.addMarker(marker);

	if(marker_onload_select != null & marker_onload_select == d.id) {
		f_event(marker,d);
		ancienne_couleur = d.couleur;
		markeur_selectionne = marker;
		marker.setUrl('../img/marker_' + ancienne_couleur + '_select.png');
	}
}


function map_with_action(url_data,action) {
	url_data_updated  = url_data;
    map = new OpenLayers.Map('map');
    osm = new OpenLayers.Layer.OSM("OSM MAP");
    map.addLayer(osm);
    map.setCenter(new OpenLayers.LonLat(439883.97188583,6525243.5465938), zoom_map);
      
    var markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);

    $.getJSON(url_data, function(data) {
	$.each(data, function(i,row_db) {
	    add_marker(markers, action, row_db); 
	    })
	});
}


// signalisattion
function map_and_pointer() {
    map = new OpenLayers.Map('map');
    var osm = new OpenLayers.Layer.OSM("OSM MAP");
    map.addLayer(osm);
    map.setCenter(new OpenLayers.LonLat(439883.97188583,6525243.5465938), zoom_map);
    
    map.events.register("click", map, function(e) {
	var position = map.getLonLatFromPixel(e.xy);
	document.f.lon.value = position.lon; 
	document.f.lat.value = position.lat; 

	if(input_marker == null) 
	{
	    marker_layer = new OpenLayers.Layer.Markers("Markers");
	    map.addLayer(marker_layer);
	    var size = new OpenLayers.Size(21,25);
	    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
	    var icon = new OpenLayers.Icon('../img/marker_white_test.png', size, offset);
	    input_marker = new OpenLayers.Marker(position,icon);
	    marker_layer.addMarker(input_marker);
	}
	else 
	{
	    var position = map.getLonLatFromPixel(e.xy);
	    document.f.lon.value = position.lon; 
	    document.f.lat.value = position.lat; 
	    input_marker.lonlat = position;
	    marker_layer.redraw();
	}
    });
}