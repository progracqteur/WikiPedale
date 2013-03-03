// override sha1.js default setting.
b64pad  = "=";

var map;
var osmLayer; // OSM layer
var placesLayer; // layer where existing places are drawing
var new_placeLayer;  // layer where the user can draw a new place
var zoom_map = 13; // zoom level of the map

var baseUrlsplit = Routing.getBaseUrl().split('/');
var web_dir = ''
for (i = 0; i < (baseUrlsplit.length - 1); i++)
{
    web_dir = web_dir + baseUrlsplit[i] + '/';
} 
var marker_img_url = web_dir + 'OpenLayers/img/'; // where is the dir containing the OpenLayers images

//var colors_in_marker = 1; //number of color in a marker
var c1_label = "cem";
var c2_label = undefined;
var c3_label = undefined;


var add_new_place_mode = false; // true when the user is in a mode for adding new place
var markers_and_associated_data = new Array(); // all the markers drawed on the map and the associated data

var id_markers_for = new Array();
id_markers_for['Categories'] = new Array();
id_markers_for['PlaceTypes'] = new Array();

var new_placeMarker;

var last_place_selected = null;

var townId = null;


// marker with color
var color_trad = new Array();
color_trad['0'] = 'w';
color_trad['-1'] = 'd';
color_trad['1'] = 'r';
color_trad['2'] = 'o';
color_trad['3'] = 'g';

var color_trad_text = new Array();
color_trad_text['0'] = 'blanc';
color_trad_text['-1'] = 'rejeté';
color_trad_text['1'] = 'rouge';
color_trad_text['2'] = 'orange';
color_trad_text['3'] = 'vert';

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

    if (c2_label == undefined)
    {
        return c1;
    }
    else if (c3_label == undefined)
    {
        return c1 + c2;
    }
    else
    {
        return c1 + c2 + c3;
    }
}

function unregisterUserInJson(label,email,phonenumber){
    /**
    * Returns a json string describing an unregister user.
    * @param{string} label The label/pseudo of the user.
    * @parem{string} email The email of the user.
    */
    return '{"entity":"user"' 
        + ',"id":null'
        + ',"label":' + JSON.stringify(label)
        + ',"email":' + JSON.stringify(email)
        + ',"phonenumber":' + JSON.stringify(phonenumber)
        + '}';
}

function PointInJson(lon,lat){
    /**
    * Returns a json string describing a point.
    * @param{string} lon The longitude of the point.
    * @param{string} lat} The latitude of the point.
    */
    p = new OpenLayers.Geometry.Point(lon, lat);
    p.transform(map.getProjectionObject(), new OpenLayers.Projection('EPSG:4326'));
    parser = new OpenLayers.Format.GeoJSON();
    return parser.write(p, false);
}

function PlaceInJSonWithOtherNameValue(id, otherNameValue){
    ret = '{"entity":"place"';
    ret = ret + ',"id":' + JSON.stringify(id);
    ret = ret + otherNameValue;
    return ret + '}';   
}

function EditDescriptionDescInJson(id,description){
    return PlaceInJSonWithOtherNameValue(id,',"description":' + JSON.stringify(description));
}

function EditDescriptionLocInJson(id,loc){
    return PlaceInJSonWithOtherNameValue(id,',"addressParts":{"entity":"address","road":' + JSON.stringify(loc) + '}');
}

function EditDescriptionCatInJson(id,cat){
    categories_desc = categories_desc + ',"categories":[';
    for (var i = 0; i < cat.length; i++) {
        categories_desc = categories_desc + '{"entity":"category","id":' + cat[i] + '}';
        if (i < (cat.length - 1))
        {
            categories_desc = categories_desc + ',';
        }
    }
    categories_desc = categories_desc + ']';
    return PlaceInJSonWithOtherNameValue(id,categories_desc);
}

function EditDescriptionStatusInJson(id,status_type,status_value){
    return PlaceInJSonWithOtherNameValue(id,',"statuses":[{"t":"' + status_type + '","v":"' + status_value + '"}]');
}

function EditDescriptionGestionaireInJson(id,gestionaire_id){
    return PlaceInJSonWithOtherNameValue(id,',"manager": {"entity":"group","type":"MANAGER","id":' 
        + JSON.stringify(gestionaire_id)  + '}');
}

function EditDescriptionPlacetypeInJson(id,placetype_id){
    return PlaceInJSonWithOtherNameValue(id,',"placetype":{"id":' +  JSON.stringify(placetype_id) + ',"entity":"placetype"}');
}

function DeleteDescriptionInJson(id){
    return PlaceInJSonWithOtherNameValue(id,',"accepted":false');
}

function PlaceInJson(description, lon, lat, address, id, color, user_label, user_email, user_phonenumber, categories) {
    /**
    * Returns a json string used for adding a new place.
    * @param {string} description the description of the new place.
    * @param {string} lon The longitude of the new place.
    * @param {string} lat The latitude of the new place.
    * @param {string} address The address of the new place.
    * @param {string} id The id of the new place, this parameter is optionnal : if it isn't given or null it means tha the place is a new placa.
    * @param {string} color The color of the place (only for existing place)
    * @param {string} user_label The label given by the user : if the user is register and logged this field is not considered
    * @param {string} user_email The email given by the user : if the user is register and logged this field is not considered
    * @param {string} user_phonenumber The phonenumber given by the user : if the user is register and logged this field is not considered
    * @param {array of string} caterogies The ids of categories selected
    */
    ret = '{"entity":"place"';

    if(id==undefined || id==null)
        { ret = ret + ',"id":null'; }
    else
        { 
            ret = ret + ',"id":' + JSON.stringify(id);
        }

    if(lon!=undefined && lon!=null && lat!=undefined && lon!=null)
    {
        ret = ret + ',"geom":'+ PointInJson(lon,lat);
    }
    if( !userIsRegister() && (user_label != undefined || user_email != undefined))
        { ret = ret + ',"creator":' + unregisterUserInJson(user_label, user_email, user_phonenumber); }

    ret = ret + ',"description":' + JSON.stringify(description)
        + ',"addressParts":{"entity":"address","road":' + JSON.stringify(address) + '}'

    ret = ret + ',"categories":[';
    for (var i = 0; i < categories.length; i++) {
        ret = ret + '{"entity":"category","id":' + categories[i] + '}';
        if (i < (categories.length - 1))
        {
            ret = ret + ',';
        }
    }
    ret = ret + ']';
    return ret + '}';
}

function update_markers_and_associated_data(){
    // removing the information
    $.each(markers_and_associated_data, function(index, marker_and_data) { 
        if (marker_and_data != undefined) { 
            delete marker_and_data[1]; 
        }
    });

    jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
            $.each(data.results, function(index, aPlaceData) {
                if (markers_and_associated_data[aPlaceData.id] == undefined) {
                    addMarkerWithClickAction(false,
                        aPlaceData.geom.coordinates[0],
                        aPlaceData.geom.coordinates[1],
                        displayPlaceDataFunction,
                        aPlaceData);
                }
                else {
                    markers_and_associated_data[aPlaceData.id][1] = aPlaceData;
                }
            });
        },
        complete: function(data) {
            $.each(markers_and_associated_data, function(index, marker_and_data) {
                if (marker_and_data != undefined) { 
                    if (marker_and_data[1] == undefined) {
                        marker_and_data[0].erase();
                        marker_and_data = undefined;
                    }
                }
            });
        }
    });
};


function updatePageWhenLogged(){
    /**
    * Updates the menu when the user is logged :
    * - connexion link and register link : disappear
    * - user name and logout link : appear
    */
    document.getElementById("menu_user_name").style.display = 'inline';
    document.getElementById("menu_connexion").style.display = 'none';
    document.getElementById("menu_logout").style.display = 'inline';
    document.getElementById("menu_register").style.display = 'none';
    jQuery('a.connexion').colorbox.close('');
    jQuery('.username').text(user.label);

    displayRegardingToUserRole();
}

function catchLoginForm(){
    /**
    * When the login form is throwed.
    * Asks to the db if couple username/password is correct
    */
    var user_data = {};
    $.map($('#loginForm').serializeArray(), function(n, i){
        user_data[n['name']] = n['value'];
    });

    url_login = Routing.generate('wikipedale_authenticate', {_format: 'json'});
    $.ajax({
        type: "POST",
        beforeSend: function(xhrObj){
            xhrObj.setRequestHeader("Authorization",'WSSE profile="UsernameToken"');
            xhrObj.setRequestHeader("X-WSSE",wsseHeader(user_data['username'], user_data['password']));
        },
        data: "",
        url: url_login,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                console.log("catchLoginForm - output success" + JSON.stringify(output_json.results[0]));
                updateUserInfo(output_json.results[0]);
                updatePageWhenLogged();
            }
            else { 
                $('#login_message').text(output_json[0].message);
                $('#login_message').addClass('errorMessage');
                }
        },
        error: function(output_json) {
            $('#login_message').text(output_json.responseText);
            $('#login_message').addClass('errorMessage');
        }
    });
}

function descriptionEdit(element_type){
    element_id = "#span_place_description_" + element_type;
    signalement_id = $('#input_place_description_id').val();

    if (element_type == 'cat'){
        categories_selected = Array();
        $.each(markers_and_associated_data[signalement_id][1].categories, function(i,c) { categories_selected.push(c.id); });
        $(element_id + '_edit').select2("val", categories_selected);
    } else if (element_type == 'status') {
        color_selected = 0;
        $.each(markers_and_associated_data[signalement_id][1].statuses, function(i,s) { if(s.t == c1_label) color_selected = s.v });
        console.log(color_selected);
        $(element_id + '_edit').select2("val", color_selected);
    }
    else {
        $(element_id + '_edit').val($(element_id).text());
    };
    $(element_id).hide();
    $("#div_place_description_" + element_type + '_edit').show();
    $(element_id + '_button').text("Sauver");
    $(element_id + '_button').attr("onClick","descriptionSaveEdit('" + element_type + "')");
};

function descriptionDelete(){
    signalement_id = $('#input_place_description_id').val();
    json_request = DeleteDescriptionInJson(signalement_id);
    url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
    $.ajax({
        type: "POST",
        data: {entity: json_request},
        url: url_edit,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                markers_and_associated_data[signalement_id][0].erase();
                markers_and_associated_data[signalement_id] = undefined;
                $('#div_placeDescription').hide();
                last_place_selected = null;
            }
            else { 
                $('#span_place_description_delete_error').show();
                console.log('Error else');
                console.log(JSON.stringify(output_json));
            }
        },
        error: function(output_json) {
            $('#span_place_description_delete_error').show();
            console.log('Error error');
            console.log(output_json.responseText);
        }
    });
}

function descriptionSaveEdit(element_type){
    element_id = "#span_place_description_" + element_type;
    signalement_id = $('#input_place_description_id').val();

    if(element_type == "desc") {
        json_request = EditDescriptionDescInJson(signalement_id,$(element_id + '_edit').val());
    }
    else if (element_type == "loc") {
        json_request = EditDescriptionLocInJson(signalement_id,$(element_id + '_edit').val());
    }
    else if (element_type == "cat") {
        json_request = EditDescriptionCatInJson(signalement_id,$(element_id + '_edit').select2("val"));
    } 
    else if (element_type == "status") {
        json_request = EditDescriptionStatusInJson(signalement_id,c1_label,$(element_id + '_edit').select2("val"));
    }
    else if (element_type == "gestionaire") {
        json_request = EditDescriptionGestionaireInJson(signalement_id,$(element_id + '_edit').select2("val"));
    }
    else if (element_type == "type"){
        json_request = EditDescriptionPlacetypeInJson(signalement_id,$(element_id + '_edit').select2("val"));
    }
    console.log(json_request);
    url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
    $.ajax({
        type: "POST",
        data: {entity: json_request},
        url: url_edit,
        cache: false,
        success: function(output_json) { 
            if(! output_json.query.error) { 
                markers_and_associated_data[signalement_id][1] = output_json.results[0];
                if(element_type == 'cat'){
                    categories_list = "";
                    $.each(markers_and_associated_data[signalement_id][1].categories, function(i,c) { categories_list = categories_list + c.label; + " "});
                    $(element_id).text(categories_list);    
                } else if (element_type == 'status'){
                    markers_and_associated_data[signalement_id][0].setUrl(marker_img_url + 'm_' + marker_img_name(markers_and_associated_data[signalement_id][1].statuses) + '_selected.png')
                    $(element_id).text(color_trad_text[$(element_id + '_edit').val()]);
                } else if (element_type == 'gestionaire' || element_type == 'type'){
                    $(element_id).text($(element_id + '_edit').select2('data').text);
                }
                else {
                    $(element_id).text($(element_id + '_edit').val());
                }
                $(element_id +  '_error').hide();
                $("#div_place_description_" + element_type + '_edit').hide();
                $(element_id).show();
                $(element_id + '_button').text("Editer");
                $(element_id + '_button').attr("onClick","descriptionEdit('" + element_type + "')");
            }
            else { 
                $(element_id +  '_error').show();
                console.log('Error else');
                console.log(JSON.stringify(output_json));
            }
        },
        error: function(output_json) {
            $(element_id +  '_error').show();
            console.log('Error error');
            console.log(output_json.responseText);
        }
    });
};

function catchForm(formName) {
    /**
    * Catch a from and save the data in the db using a json request.
    * @param{string} formName The name of the form. It is '#editForm' if it is the edition form and '#new_placeForm' if it is the new place form.
    */
    editForm = formName == '#editForm';

    var place_data = {};
    place_data['categories'] = Array();
    $.map($(formName).serializeArray(), function(n, i){
        if (n['name'] == 'categories')
        { 
            place_data['categories'].push(n['value']);
        }
        else
        {
            place_data[n['name']] = n['value'];
        }
    });

    error_messages = "";
    if(place_data['description'] == "") { 
        error_messages = error_messages + "Veuillez remplir la description. ";
        update_d_informer_for_form();
        }
    if(place_data['lieu'] == "") {
        error_messages = error_messages + "Veuillez indiquer l'adresse. ";
        update_l_informer_for_form();
    }
    if(! userIsRegister())
    {
        if(place_data['user_label'] == "") { 
            error_messages = error_messages + "Veuillez donner votre nom. ";
            update_n_informer_for_form();
            }
    if(! is_mail_valid(place_data['email'])) {
        error_messages = error_messages + "Veuillez indiquer une adresse email valide. ";
        update_e_informer_for_form();
    }

    }
    if( ! editForm)
    {
        if(place_data['lon'] == "" || place_data['lat'] == "") {
            error_messages = error_messages + "Veuillez indiquer où se trouve le point noir en cliquant sur la carte. ";
            update_informer_map_not_ok();
            }
    }
    else
    {
        if (place_data['couleur'] == "")
            { error_messages =  error_messages +  "Veuillez indiquer la couleur du point noir. " }
        if (place_data['id'] == "")
            { error_messages = "Veuillez reéssayer et si le problème persiste prendre contact avec le webmaster. " }
    }
    /* Ne marche pas car pbm de synchonisation */
    //A regler TODO
    isUserInAccordWithServer().done(function(userInAccordWithServer)
        {
        if(!userInAccordWithServer)
            {
                $('#login_message').text("Veuillez vous reconnecter.")
                $.colorbox({inline:true, href:"#login_form_div"});
            }
        else {
            if(editForm && userIsAdminServer())
                { error_messages = "Vous devez être admin pour éditer ce point noir"; }
            if(error_messages != "") {
                $('#new_placeFrom_message').text('Erreur! ' + error_messages  + 'Merci.');
                $('#new_placeFrom_message').addClass('errorMessage');
                }
            else {
                entity_string = PlaceInJson(place_data['description'], place_data['lon'],
                    place_data['lat'], place_data['lieu'], place_data['id'], place_data['couleur'],
                    place_data['user_label'], place_data['email'], place_data['user_phonenumber'],place_data['categories']);
                console.log(entity_string);
                url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
                $.ajax({
                    type: "POST",
                    data: {entity: entity_string},
                    url: url_edit,
                    cache: false,
                    success: function(output_json) { 
                        if(! output_json.query.error) { 
                            newPlaceData = output_json.results[0];
                            addMarkerWithClickAction(false,
                                newPlaceData.geom.coordinates[0],
                                newPlaceData.geom.coordinates[1],
                                displayPlaceDataFunction,
                                newPlaceData);
                            if(! editForm) {
                                $('#new_placeFrom_message').text("Le point noir que vous avez soumis a bien été enregistré. Merci!");
                                setTimeout(
                                    function(){
                                        changingModeFunction();
                                        document.getElementById("div_placeEdit").style.display = "none";
                                        clearNewPlaceForm();
                                        displayPlaceDataFunction(markers_and_associated_data[newPlaceData.id][0],markers_and_associated_data[newPlaceData.id][1]);
                                    },3000);        
                                }
                            else {
                                $('#new_placeFrom_message').text("Le point noir a bien été modifié. Merci!");
                            }
                            $('#new_placeFrom_message').addClass('successMessage');
                            $('#new_place_form_submit_button').attr("disabled", "disabled");
                        }
                        else { 
                            alert(output_json[0].message);
                            alert('ERREUR'); } },
                    error: function(output_json) {
                        alert(JSON.stringify(output_json));
                        alert(output_json.responseText);
                        alert(JSON.parse(output_json.responseText)[0]);
                        alert(JSON.stringify(JSON.parse(output_json.responseText)[0]));
                        alert((output_json.responseText[0]).message);
                        alert('ERREUR'); 
                    }
                });
            }
        }
    });
}

function clearNewPlaceForm() {
    /** 
    * Clear the data entered in the form with id 'new_placeForm'
    */
    document.getElementById("new_placeForm").user_phonenumber.value = "";
    document.getElementById("new_placeForm").user_label.value = "";
    document.getElementById("new_placeForm").user_label.readOnly=false;
    document.getElementById("new_placeForm").email.value = "";
    document.getElementById("new_placeForm").email.readOnly=false;
    document.getElementById("new_placeForm").email.value = "";
    document.getElementById("new_placeForm").lieu.value = "";
    document.getElementById("new_placeForm").description.value = "";
    document.getElementById("new_placeForm").lon.value = "";
    document.getElementById("new_placeForm").lat.value = "";
    reset_informer();
    $('#new_placeFrom_message').text("");
    $('#new_place_form_submit_button').removeAttr("disabled");   
}

function changingModeFunction() {
    /**
    * Changin the mode between 'add_new_place' and 'edit_place' / 'show_place'.
    */
    if(!add_new_place_mode) {
        $('.olControlButtonAddPlaceItemActive').each(function(index, value){
            value.innerHTML = 'Annuler';
        });
        $.each(markers_and_associated_data, function(index, marker_data) {
            if (marker_data != undefined) {
                marker = marker_data[0];
                marker.events.remove("mousedown");
                marker.setUrl(marker_img_url + 'm_' + marker_img_name(marker_data[1].statuses) + '_no_active.png')
            }
        });

        if(new_placeMarker != undefined) 
            {
                new_placeMarker.display(true);
            }

        map.events.register("click", map, function(e) {
            update_informer_map_ok(); //le croix rouge dans le formulaire nouveau point devient verte
            var position = map.getLonLatFromPixel(e.xy);
            $("input[name=lon]").val(position.lon);
            $("input[name=lat]").val(position.lat);

            if(new_placeMarker == undefined) 
                {
                    var size = new OpenLayers.Size(19,25);
                    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
                    var icon = new OpenLayers.Icon(marker_img_url + 'm_' + marker_img_name([]) + '_selected.png', size, offset); 
                    new_placeMarker = new OpenLayers.Marker(position,icon);
                    placesLayer.addMarker(new_placeMarker);
                }
                else 
                {
                    new_placeMarker.lonlat = position;
                    placesLayer.redraw();
                }
            });

            if(userIsRegister()) {
                document.getElementById("div_new_place_form_user_mail").style.display = 'none';
                }
            else {
                document.getElementById("div_new_place_form_user_mail").style.display = 'block';
            }
            document.getElementById("div_signaler").style.display = "block";
            //document.getElementById("div_placeDetails").style.display = "none";
            //document.getElementById("div_placeEdit").style.display = "none";
            document.getElementById("div_placeDescription").style.display = "none";

            add_new_place_mode = true;
        }
        else {
            $('.olControlButtonAddPlaceItemActive').each(function(index, value){
                value.innerHTML = 'Ajouter un point';
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
                            displayPlaceDataFunction(markers_and_associated_data[iid][0],markers_and_associated_data[iid][1]);
                            OpenLayers.Event.stop(evt);
                        } }
                    ) (data.id);

                    marker.events.register("mousedown", marker, markerMouseDownFunction);

                    if(last_place_selected != null  && last_place_selected == data.id) {
                        marker.setUrl(marker_img_url + 'm_' + marker_img_name(data.statuses) + '_selected.png');
                        }
                    else {
                        marker.setUrl(marker_img_url + 'm_' + marker_img_name(data.statuses) + '.png');
                    }
                }
            });

            document.getElementById("div_signaler").style.display = "none";

            if(last_place_selected != null ) {
                document.getElementById("div_placeDescription").style.display = "block";
                //if(userIsAdmin()) { document.getElementById("div_placeEdit").style.display = "block"; }
                //else { document.getElementById("div_placeDetails").style.display = "block"; }
            }
            add_new_place_mode = false; 
        }
    };

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

    var control_panel = new OpenLayers.Control.Panel({
        div: document.getElementById('olPanelUL')});
    map.addControl(control_panel);
    control_panel.addControls([button_add_place]);
    
    button_add_place.activate();

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive').each(function(index, value){
            value.innerHTML = 'Ajouter un point';
        });
    });
    $.getJSON(jsonUrlData, function(data) {
    updateUserInfo(data.user);
	$.each(data.results, function(index, aPlaceData) {
	    addMarkerWithClickAction(false,
				     aPlaceData.geom.coordinates[0],
				     aPlaceData.geom.coordinates[1],
				     displayPlaceDataFunction,
				     aPlaceData);
        if(aPlaceData.id == marker_id_to_display)
        {
            displayPlaceDataFunction(markers_and_associated_data[marker_id_to_display][0],markers_and_associated_data[marker_id_to_display][1]);
        }

         } ) }
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

// PHOTO
function pop_up_add_photo(i) {
    window.open(Routing.generate('wikipedale_photo_new', {_format: 'html', placeId: i}));
}

function refresh_span_photo(id) {
    url_photo_list = Routing.generate('wikipedale_photo_list_by_place', {_format: 'json', placeId: id});
    $.getJSON(url_photo_list, function(raw_data) {
    data = raw_data.results;
    if(data.length == 0) {
        $('.span_photo').each(function() { this.innerHTML = '<img src="../img/NoPictureYet.png" />'; });
        }
    else {
        span_photo_inner = '<br />';
        $.each(data, function(i,row) {
        span_photo_inner +=  ' <a target="_blank" href="' + web_dir + row.webPath + '"><image height="' + row.height  + '"width="' + row.width  + '" src="' + web_dir + row.webPath + '"></image></a>';
        $('.span_photo').each(function() { this.innerHTML = span_photo_inner; });
        })
        }
    });
}

function displayRegardingToUserRole() {
    if(userCanModifyCategories() || userIsAdmin()) {
        $('#span_place_description_cat_button').show();
    }
    else {
        $('#span_place_description_cat_button').hide();
    }

    if(userCanModifyLittleDetails() || userIsAdmin()) {
        $('#span_place_description_loc_button').show();
        $('#span_place_description_desc_button').show();
    }
    else {
        $('#span_place_description_loc_button').hide();
        $('#span_place_description_desc_button').hide();
    }

    if(userCanModifyPlacetype() || userIsAdmin()) {
        $('#span_place_description_type_button').show();
    }
    else {
        $('#span_place_description_type_button').hide();
    }

    if(userCanModifyManager() || userIsAdmin()) {
        $('#span_place_description_gestionaire_button').show();
    }
    else {
        $('#span_place_description_gestionaire_button').hide();
    }

    if(userCanUnpublish() || userIsAdmin()){
        $('#span_place_description_delete_button').show();
    }
    else {
        $('#span_place_description_delete_button').hide();
    }

    if(userCanModifyCEMColor() || userIsAdmin()){
        $('#span_place_description_status_button').show();
    }
    else {
        $('#span_place_description_status_button').hide();
    }

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
    if (last_place_selected != null) {
        markers_and_associated_data[last_place_selected][0].setUrl(
            marker_img_url + 'm_' + marker_img_name(markers_and_associated_data[last_place_selected][1].statuses) + '.png'
            );
    }
    placeMarker.setUrl(marker_img_url + 'm_' + marker_img_name(placeData.statuses) + '_selected.png');
    console.log("place info:" + JSON.stringify(placeData));
    last_place_selected = placeData.id;
    refresh_span_photo(placeData.id);
    url_add_photo = "javascript:pop_up_add_photo(" + placeData.id + ")";
    $('a.link_add_photo').each(function() { $(this).attr("href", url_add_photo)});

    categories_list = "";
    $.each(placeData.categories, function(i,c) { categories_list = categories_list + c.label; + " "});

    $('.class_span_place_description_id').each(function() { this.innerHTML = placeData.id; });
    $('.class_span_place_description_loc').each(function() { this.innerHTML = placeData.addressParts.road; });
    $('#input_place_description_id').val(placeData.id);
    $('#span_place_description_signaleur').text(placeData.creator.label);
    $('#span_place_description_loc').text(placeData.addressParts.road);
    $('#span_place_description_desc').text(placeData.description);
    $('#span_place_description_cat').text(categories_list);

    if (placeData.placetype == null){
        $('#span_place_description_type').text("pas encore de type assigné");
    }
    else {
        $('#span_place_description_type').text(placeData.placetype.label);
    }
    if (placeData.manager == null) {
        $('#span_place_description_gestionaire').text("pas encore de gestionaire assigné");
    }
    else{
        $('#span_place_description_gestionaire').text(placeData.manager.label);
    }
    

    $('#span_place_description_status').text(color_trad_text[0]);
    for (i = 0; i < placeData.statuses.length; i++)
        {  
            if (placeData.statuses[i].t == 'cem')
            {
                $('#span_place_description_status').text(color_trad_text[placeData.statuses[i].v]); 
            }
        }

    if (userCanVieuwUsersDetails() || userIsAdmin()) {
        $('#span_place_description_signaleur_contact').html('(email : <a href="mailto:'+ placeData.creator.email +'">'+ 
        placeData.creator.email +'</a>, téléphone : '+ placeData.creator.phonenumber + ')');
    }

    displayRegardingToUserRole();

    $('#div_placeDescription').show();
}

