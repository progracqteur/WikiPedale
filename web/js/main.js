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
var img_url = web_dir + 'OpenLayers/img/'; // where is the dir containing the OpenLayers images


var add_new_place_mode = false; // true when the user is in a mode for adding new place
var markers_and_associated_data = Array(); // all the markers drawed on the map and the associated data

var new_placeMarker;

var last_place_selected = null;

$.ajaxSetup({ cache: false }); // IE save json data in a cache, this line avoids this behavior

function blopFunction() {
    /**
    * Testing function
    */
        alert('blop');
    }

function unregisterUserInJson(label,email){
    /**
    * Returns a json string describing an unregister user.
    * @param{string} label The label/pseudo of the user.
    * @parem{string} email The email of the user.
    */
    return '{"entity":"user"' 
        + ',"id":null'
        + ',"label":' + JSON.stringify(label)
        + ',"email":' + JSON.stringify(email)
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

function PlaceInJson(description, lon, lat, address, id, color, user_label, user_email) {
    /**
    * Returns a json string used for adding a new place.
    * @param {string} description the description of the new place.
    * @param {string} lon The longitude of the new place.
    * @param {string} lat The latitude of the new place.
    * @param {string} address The address of the new place.
    * @param {string} id The id of the new place, this parameter is optionnal : if it isn't given or null it means tha the place is a new placa.
    * @param {string} coulor The color of the place (only for existing place)
    * @param {string} user_label The label given by the user : if the user is register and logged this field is not considered
    * @param {string} user_email The email given by the user : if the user is register and logged this field is not considered
    */
    ret = '{"entity":"place"'

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
        { ret = ret + ',"creator":' + unregisterUserInJson(user_label, user_email); }

    return ret + ',"description":' + JSON.stringify(description)
        + ',"addressParts":{"entity":"address","road":' + JSON.stringify(address) + '}'
        + '}';
}

function updatePageWhenLogged(){
    document.getElementById("menu_user_name").style.display = 'inline';
    document.getElementById("menu_connexion").style.display = 'none';
    document.getElementById("menu_profile").style.display = 'inline';
    document.getElementById("menu_logout").style.display = 'inline';
    document.getElementById("menu_register").style.display = 'none';
    jQuery('a.connexion').colorbox.close('');
    jQuery('.username').text(user.label);
}

function catchLoginForm(){
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
                updatePageWhenLogged()
            }
            else { 
                alert(output_json[0].message);
                alert('ERREUR_1'); }
        },
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

function catchForm(formName) {
    /**
    * Catch a from and save the data in the db using a json request.
    * @param{string} formName The name of the form. It is '#editForm' if it is the edition form and '#new_placeForm' if it is the new place form.
    */
    editForm = formName == '#editForm';

    var place_data = {};
    $.map($(formName).serializeArray(), function(n, i){
        place_data[n['name']] = n['value'];
    });

    error_messages = "";
    if(place_data['description'] == "")
        { error_messages = error_messages + "Veuillez remplir la description. "  }
    if(place_data['lieu'] == "")
        { error_messages = error_messages + "Veuillez indiquer l'adresse. "  }
    if( ! editForm )
    {
        if(place_data['lon'] == "" || place_data['lat'] == "")
            { error_messages = error_messages + "Veuillez indiquer où se trouve le point noir en cliquant sur la carte. "  }
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
        alert(userInAccordWithServer);
        if(!userInAccordWithServer)
            {
                $('#login_message').text("Veuillez vous reconnecter.")
                $.colorbox({inline:true, href:"#login_user_form_div"});
            }
        else {
            if(editForm && userIsAdminServer())
                { error_messages = "Vous devez être admin pour éditer ce point noir"; }
            if(error_messages != "") { alert('Erreur! ' + error_messages  + 'Merci.'); }
            else {
                entity_string = PlaceInJson(place_data['description'], place_data['lon'],
                    place_data['lat'], place_data['lieu'], place_data['id'], place_data['couleur'],
                    place_data['user_label'], place_data['email']);
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
                            alert("Le point noir que vous avez soumis a bien été enregistré. Merci!");
                            changingModeFunction();
                            document.getElementById("div_placeEdit").style.display = "none";
                            clearNewPlaceForm();
                            displayPlaceDataFunction(markers_and_associated_data[newPlaceData.id][0],markers_and_associated_data[newPlaceData.id][1]);
                            }
                        else {
                            alert("Le point noir a bien été modifié. Merci!");
                        }
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
                    alert('ERREUR');  }
                });
            }
        }
    });
}

function clearNewPlaceForm() {
    /** 
    * Clear the data entered in the form with id 'new_placeForm'
    */
    document.getElementById("new_placeForm").user_label.value = "";
    document.getElementById("new_placeForm").user_label.readOnly=false;
    document.getElementById("new_placeForm").email.value = "";
    document.getElementById("new_placeForm").email.readOnly=false;
    document.getElementById("new_placeForm").email.value = "";
    document.getElementById("new_placeForm").lieu.value = "";
    document.getElementById("new_placeForm").description.value = "";
    document.getElementById("new_placeForm").lon.value = "";
    document.getElementById("new_placeForm").lat.value = "";
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
                marker.setUrl(img_url + 'marker-gold.png')
            }
        });

        if(new_placeMarker != undefined) 
            {
                new_placeMarker.display(true);
            }

        map.events.register("click", map, function(e) {
            update_informer_map(); //le croix rouge dans le formulaire nouveau point devient verte
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

            if(userIsRegister()) {
                document.getElementById("div_new_place_form_user_mail").style.display = 'none';
                }
            else {
                document.getElementById("div_new_place_form_user_mail").style.display = 'block';
            }
            document.getElementById("div_signaler").style.display = "block";
            document.getElementById("div_placeDetails").style.display = "none";
            document.getElementById("div_placeEdit").style.display = "none";
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
                            /*
                            alert('blop');
                            alert(markers_and_associated_data[iid][1]);
                            alert(markers_and_associated_data[iid][0]);
                            */
                            displayPlaceDataFunction(markers_and_associated_data[iid][0],markers_and_associated_data[iid][1]);
                            OpenLayers.Event.stop(evt);
                        } }
                    ) (data.id);

                    marker.events.register("mousedown", marker, markerMouseDownFunction);
                    marker.setUrl(img_url + 'marker.png')
                }
            });

            document.getElementById("div_signaler").style.display = "none";

            if(last_place_selected != null ) {
                if(userIsAdmin()) { document.getElementById("div_placeEdit").style.display = "block"; }
                else { document.getElementById("div_placeDetails").style.display = "block"; }
            }
            add_new_place_mode = false; 
        }
    };


function homepageMap(townId, townLon, townLat) {
    /**
     * TODO -> changer le nom et voir pour la gestion ce qui peut etre reutiliser
     * @param {townId} TODO
     * @param {townLon} TODO
     * @param {townLat} TODO
     * @param {clickAction} TODO
     */

    jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId, addUserInfo: true});

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
    control_panel.addControls([button_add_place, button_lately_added, button_lately_updated ]);
    
    button_add_place.activate();
    button_lately_added.activate();
    button_lately_updated .activate();

    $(document).ready(function(){
        $('.olControlButtonAddPlaceItemActive').each(function(index, value){
            value.innerHTML = 'Ajouter un point';
        });
    });
    /* $(document).ready(function(){
        $('.olControlButtonLatelyAddedItemActive').each(function(index, value){
            value.innerHTML = 'Derniers ajoutés';
        });
    });
    $(document).ready(function(){
        $('.olControlButtonLatelyUpdatedItemActive').each(function(index, value){
            value.innerHTML = 'Derniers modifiés';
        });
    }); */
    $.getJSON(jsonUrlData, function(data) {
    updateUserInfo(data.user);
	$.each(data.results, function(index, aPlaceData) {
        //alert(JSON.stringify(aPlaceData));
	    addMarkerWithClickAction(false,
				     aPlaceData.geom.coordinates[0],
				     aPlaceData.geom.coordinates[1],
				     displayPlaceDataFunction,
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

function displayPlaceDataFunction(placeMarker, placeData) {
    /**
     * Function which display some data of the place on the webpage.
     executed when the user click on a marker on the index page.
     For this page, a marker represents a place
     * @param {OpenLayers.Marker} placeMarker The marker clicked
     * @param {object} placeData The know data given for the place and receivd from 
     web/app_dev.php/place/list/bycity.json?city=mons
     */
    console.log("place info:" + JSON.stringify(placeData));
    last_place_selected = placeData.id;
    $('.span_id').each(function() { this.innerHTML = placeData.id; });
    $('.span_nbComm').each(function() { this.innerHTML = placeData.nbComm; });
    $('.span_nbVote').each(function() { this.innerHTML = placeData.nbVote; });
    $('.span_creator').each(function() { this.innerHTML = placeData.creator.label; });
    refresh_span_photo(placeData.id);
    url_add_photo = "javascript:pop_up_add_photo(" + placeData.id + ")";
    $('a.link_add_photo').each(function() { $(this).attr("href", url_add_photo)});

    if (userIsAdmin()) {
        document.getElementById("f_id").value = placeData.id;
        document.getElementById("f_lieu").value = placeData.addressParts.road;
        document.getElementById("f_description").value = placeData.description;
        document.getElementById("div_placeEdit").style.display = "block";
    }
    else {
        document.getElementById("span_lieu").innerHTML = placeData.addressParts.road;
        document.getElementById("span_description").innerHTML = placeData.description;
        document.getElementById("div_placeDetails").style.display = "block";
    }
}

