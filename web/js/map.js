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

    map_display.init(townLon,townLat);

    var button_add_place = new OpenLayers.Control.Button({ 
        id : 'buttonAddPlace',
        displayClass: 'olControlButtonAddPlace',
        trigger: changingModeFunction,
        title: 'Button is to be clicked'});

    var control_panel = new OpenLayers.Control.Panel({
        div: document.getElementById('olPanelUL')});
    map_display.map.addControl(control_panel);
    control_panel.addControls([button_add_place]);
    
    button_add_place.activate();

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive')
            .addClass("buttonPlus")
            .each(function(index, value){ value.innerHTML = 'Ajouter un signalement'; });
    });

    $.getJSON(jsonUrlData, function(data) {
    user.update(data.user);
    descriptions.update(data.results);
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
    //console.log(someData);

    descriptions.update_for_id(someData.id, someData);
    markers_and_associated_data[someData.id] = (['x',someData]);

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

    map_display.add_marker(someData.id, anEventFunction)
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
