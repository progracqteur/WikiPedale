function map_resizing(){
    if(!displaying_tiny_map) {
        $("#map")
            .width("30%")
            .height("300px");
        $("#ToolsPanel")
            .width("70%");
    }
    else {
        $("#map")
            .width("50%")
            .height("500px");
        $("#ToolsPanel")
            .width("50%");
    }
    displaying_tiny_map = ! displaying_tiny_map;
    map.updateSize();
}

function map_translate(){
    $("#map").hide();
    $("#div_placeDescription").show();
    $("#param_carte").hide();
    $("#olPanelUL").hide();
    $("#map_little").show();
    $("#div_returnNormalMode").show();
    $("#div_dernieres_modifs").hide();
    map.render("map_little");
    map.updateSize();
}


function map_untranslate(){
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
    map_untranslate();
    $("#div_last_private_comment_container").show();
    $("#span_plus_de_commenaitres_link").show();
    $("#div_list_private_comment_container").hide();
    $("#div_form_commentaires_cem_gestionnaire").hide();
    map.setCenter(old_center);
}

//*
function homepageMap(townId_param, townLon, townLat, marker_id_to_display) {
    /**
     * TODO -> changer le nom et voir pour la gestion ce qui peut etre reutiliser
     * @param {townId} id of the town
     * @param {townLon} longitude of the town
     * @param {townLat} latitude of the town
     * @param {marker_id_to_display} id of the marker to display (direct acces) // none if no marker to display
     */
    townId = townId_param;
    jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId_param, addUserInfo: true});

    map = new OpenLayers.Map('map', {maxResolution: 1000});
    osm = new OpenLayers.Layer.OSM("OSM MAP");
    //osm = new OpenLayers.Layer.Image(
    //                            'City Lights',
    //                            'http://www.webrankinfo.com/dossiers/wp-content/uploads/google-maps-france-carte.jpg');

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

    var control_panel = new OpenLayers.Control.Panel({
        div: document.getElementById('olPanelUL')});
    map.addControl(control_panel);
    control_panel.addControls([button_add_place]);
    
    button_add_place.activate();

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive')
            .addClass("buttonPlus")
            .each(function(index, value){ value.innerHTML = 'Ajouter un signalement'; });
    });
    $.getJSON(jsonUrlData, function(data) {
    updateUserInfo(data.user);
    $.each(data.results, function(index, aPlaceData) {
        addMarkerWithClickAction(aPlaceData.geom.coordinates[0],
                     aPlaceData.geom.coordinates[1],
                     displayPlaceDataFunction,
                     aPlaceData);
        if(aPlaceData.id == marker_id_to_display)
        {
            displayPlaceDataFunction(marker_id_to_display);
        }

         } ) }
         );
}
//*/

function addMarkerWithClickAction(aLon, aLat, anEventFunction, someData) {
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
    var size = new OpenLayers.Size(19,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name(someData.statuses) + '.png', size, offset); 
    feature.data.icon = icon;
    
    var marker = feature.createMarker();
    markers_and_associated_data[someData.id] = ([marker,someData]);
    $.each(someData.categories, function(index, categories_data) {
        if (id_markers_for['Categories'][categories_data.id] == undefined){
            id_markers_for['Categories'][categories_data.id] = new Array();
        }
        id_markers_for['Categories'][categories_data.id].push(someData.id);
    });
    if (someData.placetype != null) {
        if(id_markers_for['PlaceTypes'][someData.placetype.id] == undefined) {
            id_markers_for['PlaceTypes'][someData.placetype.id] = new Array();
        }
        id_markers_for['PlaceTypes'][someData.placetype.id].push(someData.id);  
    }

    if(id_markers_for['StatusCeM']["0"] == undefined) {
                id_markers_for['StatusCeM']["0"] = new Array();
    }

    var someDataId_added = false;
    $.each(someData.statuses, function(index, type_value) {
        if(type_value.t == "cem") {
            if(id_markers_for['StatusCeM'][type_value.v.toString()] == undefined) {
                id_markers_for['StatusCeM'][type_value.v.toString()] = new Array();
            }
            id_markers_for['StatusCeM'][type_value.v.toString()].push(someData.id)
            someDataId_added = true;
        }
    });
    if(! someDataId_added) {
        id_markers_for['StatusCeM']["0"].push(someData.id)
    }

    var markerMouseDownFunction = function(evt) {
    anEventFunction(someData.id); 
        OpenLayers.Event.stop(evt);
    };

    marker.events.register("mousedown", marker, markerMouseDownFunction);
    placesLayer.addMarker(marker);
}

function marker_img_name(statuses)
{
    c1 = 'w';
    c2 = 'w';
    c3 = 'w';
    for (i = 0; i < (statuses.length); i++)
    {
        if (statuses[i].t == c1_label) {
            c1 = color_trad[statuses[i].v];
        }

        if (c2_label != undefined && statuses[i].t == c2_label) {
            c2 = color_trad[statuses[i].v];
        }

        if (c3_label != undefined && statuses[i].t == c3_label) {
            c3 = color_trad[statuses[i].v];
        }
    }

    if (c2_label == undefined) {
        return c1;
    }
    else if (c3_label == undefined) {
        return c1 + c2;
    }
    else {
        return c1 + c2 + c3;
    }
}