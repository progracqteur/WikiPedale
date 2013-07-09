function comments_mode(aPlaceId){
    old_center =  map.getCenter();
    map_translate();
    $("#div_last_private_comment_container").hide();
    $("#span_plus_de_commenaitres_link").hide();
    $("#div_list_private_comment_container").show();
    $("#div_form_commentaires_cem_gestionnaire").show();
    $("#add_new_description_form__message").val("");
    map.setCenter(markers_and_associated_data[aPlaceId][0].lonlat);
    scroll(0,0);
}

function descriptionHideEdit(){
    /**
    * dans le div "div_placeDescription", l'utilisateur peut afficher un formulaire pour éditer
    * certaines données.
    * si il change de point, il faut afficher le nouveau point dans un mode de non-édition
    * -> se fait en appeleant cette fonction
    */
    $("#div_placeDescription div").each(function(i,e) {
        id_e = $(e).attr("id");
        if(id_e != undefined && id_e.indexOf('_edit') != -1 &&  id_e.indexOf('div_') != -1) {
            $(e).hide();
        }
    });

    $(".ButtonEdit img").each(function(i,e) {
        $(e).attr("src", "../img/edit.png")
            .attr("title", "Editer");
        });

    mode_edit = new Array();
       

    $("#div_placeDescription span").each(function(i,e) {
        id_e = $(e).attr("id");
        if(id_e != undefined 
            && id_e.indexOf('_error') == -1) {
            $(e).show();
        }
    });
};

function descriptionDelete(){
    /**
    * to delete a description
    */
    signalement_id = parseInt($('#input_place_description_id').val());
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
                //console.log('Error else');
                //console.log(JSON.stringify(output_json));
            }
        },
        error: function(output_json) {
            $('#span_place_description_delete_error').show();
            //console.log('Error error');
            //console.log(output_json.responseText);
        }
    });
}


function descriptionEditOrSave(element_type){
    element_id = "#span_place_description_" + element_type;
    signalement_id = parseInt($('#input_place_description_id').val());

    if (mode_edit[element_type] == undefined || ! mode_edit[element_type]) {
        // SHOW THE EDIT FORM
        if (element_type == 'cat'){
            categories_selected = Array();
            $.each(markers_and_associated_data[signalement_id][1].categories, function(i,c) { categories_selected.push(c.id); });
            $(element_id + '_edit').select2("val", categories_selected);
        } else if (element_type == 'status') {
            color_selected = 0;
            $.each(markers_and_associated_data[signalement_id][1].statuses, function(i,s) { if(s.t == c1_label) color_selected = s.v });
            $(element_id + '_edit').select2("val", color_selected);
        }
        else {
            $(element_id + '_edit').val($(element_id).text());
        }
        $(element_id).hide();
        $("#div_place_description_" + element_type + '_edit').show();
        $(element_id + '_button').html(
            $(document.createElement('img'))
                .attr("src", "../img/sauver.png")
                .attr("title", "Sauver"));
        mode_edit[element_type] = true;
    }
    else 
    {
        // SAVE THE FORM
        if(element_type == "commentaireCeM") {
            json_request = EditDescriptionCommentaireCeMInJson(signalement_id,$(element_id + '_edit').val());
        }
        else if(element_type == "desc") {
            json_request = EditDescriptionDescInJson(signalement_id,$(element_id + '_edit').val());
        }
        else if (element_type == "loc") {
            json_request = EditDescriptionLocInJson(signalement_id,$(element_id + '_edit').val());
        }
        else if (element_type == "cat") {
            json_request = EditDescriptionSingleCatInJson(signalement_id,$(element_id + '_edit').select2("val"));
        } 
        else if (element_type == "status") {
            json_request = EditDescriptionStatusInJson(signalement_id,c1_label,$(element_id + '_edit').select2("val"));
        }
        else if (element_type == "gestionnaire") {
            json_request = EditDescriptionGestionnaireInJson(signalement_id,$(element_id + '_edit').select2("val"));
        }
        else if (element_type == "type"){
            json_request = EditDescriptionPlacetypeInJson(signalement_id,$(element_id + '_edit').select2("val"));
        }
        url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
        $.ajax({
            type: "POST",
            data: {entity: json_request},
            url: url_edit,
            cache: false,
            success: function(output_json) { 
                if(! output_json.query.error) { 
                    old_categories = markers_and_associated_data[signalement_id][1].categories;
                    old_statuses = markers_and_associated_data[signalement_id][1].statuses;
                    old_placetype = markers_and_associated_data[signalement_id][1].placetype;
                    markers_and_associated_data[signalement_id][1] = output_json.results[0];
                    if(element_type == 'cat'){
                        categories_list = "";
                        $.each(markers_and_associated_data[signalement_id][1].categories, function(i,c) { categories_list = categories_list + c.label; + " "});
                        $(element_id).text(categories_list); 
                        $.each(old_categories, function(i,c) {
                            index_sig = id_markers_for['Categories'][c.id].indexOf(signalement_id);
                            id_markers_for['Categories'][c.id].splice(index_sig,1);
                        } );
                        $.each(markers_and_associated_data[signalement_id][1].categories, function(i,c) {
                            if (id_markers_for['Categories'][c.id] == undefined){
                                id_markers_for['Categories'][c.id] = new Array();
                            }
                            id_markers_for['Categories'][c.id].push((signalement_id));
                        });
                        display_only_markers_with_selected_categories();
                    } else if (element_type == 'status'){
                        markers_and_associated_data[signalement_id][0].setUrl(marker_img_url + 'm_' + marker_img_name(markers_and_associated_data[signalement_id][1].statuses) + '_selected.png')
                        $(element_id).text(color_trad_text[$(element_id + '_edit').val()]);

                        $.each(old_statuses, function(index, type_value) {
                            if(type_value.t == "cem") {
                                index_sig = id_markers_for['StatusCeM'][type_value.v].indexOf(signalement_id);
                                id_markers_for['StatusCeM'][type_value.v].splice(index_sig,1);
                            }
                        });

                        if(id_markers_for['StatusCeM']["0"] == undefined) {
                            id_markers_for['StatusCeM']["0"] = new Array();
                        }

                        var someDataId_added = false;
                        $.each(markers_and_associated_data[signalement_id][1].statuses, function(index, type_value) {
                            if(type_value.t == "cem") {
                                if(id_markers_for['StatusCeM'][type_value.v.toString()] == undefined) {
                                    id_markers_for['StatusCeM'][type_value.v.toString()] = new Array();
                                }
                                id_markers_for['StatusCeM'][type_value.v.toString()].push(parseInt(signalement_id))
                                someDataId_added = true;
                            }
                        });
                        
                        if(! someDataId_added) {
                            id_markers_for['StatusCeM']["0"].push(signalement_id)
                        }

                        display_only_markers_with_selected_categories();
                    }
                    else if (element_type == 'gestionnaire') {
                        $(element_id).text($(element_id + '_edit').select2('data').text);
                    }
                    else if (element_type == 'type'){
                        $(element_id).text($(element_id + '_edit').select2('data').text);
                        if (old_placetype != null) {   
                            index_sig = id_markers_for['PlaceTypes'][old_placetype.id].indexOf(signalement_id);
                            id_markers_for['PlaceTypes'][old_placetype.id].splice(index_sig,1);
                        }

                        if (markers_and_associated_data[signalement_id][1].placetype != null) {
                            if(id_markers_for['PlaceTypes'][markers_and_associated_data[signalement_id][1].placetype.id] == undefined) {
                                id_markers_for['PlaceTypes'][markers_and_associated_data[signalement_id][1].placetype.id] = new Array();
                            }
                            id_markers_for['PlaceTypes'][markers_and_associated_data[signalement_id][1].placetype.id].push(parseInt(signalement_id))
                        }

                        display_only_markers_with_selected_categories();
                    }
                    else {
                        $(element_id).text($(element_id + '_edit').val());
                    }
                    $(element_id +  '_error').hide();
                    $("#div_place_description_" + element_type + '_edit').hide();
                    $(element_id).show();
                    $(element_id + '_button').html(
                        $(document.createElement('img'))
                            .attr("src", "../img/edit.png")
                            .attr("title", "Editer"));
                    mode_edit[element_type] = false;
                }
                else { 
                    $(element_id +  '_error').show();
                    //console.log('Error else');
                    //console.log(JSON.stringify(output_json));
                }
            },
            error: function(output_json) {
                $(element_id +  '_error').show();
                //console.log('Error error');
                //console.log(output_json.responseText);
            }
        });
    };
    return false;
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
        }
    if(place_data['lieu'] == "") {
        error_messages = error_messages + "Veuillez indiquer l'adresse. ";
    }
    if(! user.isRegistered())
    {
        if(place_data['user_label'] == "") { 
            error_messages = error_messages + "Veuillez donner votre nom. ";
            }
    if(! is_mail_valid(place_data['email'])) {
        error_messages = error_messages + "Veuillez indiquer une adresse email valide. ";
    }

    }
    if( ! editForm)
    {
        if(place_data['lon'] == "" || place_data['lat'] == "") {
            error_messages = error_messages + "Veuillez indiquer où se trouve le point noir en cliquant sur la carte. ";
            }
    }
    else
    {
        if (place_data['couleur'] == "")
            { error_messages =  error_messages +  "Veuillez indiquer la couleur du point noir. " }
        if (place_data['id'] == "")
            { error_messages = "Veuillez reéssayer et si le problème persiste prendre contact avec le webmaster. " }
    }

    user.isInAccordWithServer().done(function(userInAccordWithServer)
        {
        if(!userInAccordWithServer)
            {
                $('#login_message').text("Veuillez vous reconnecter.")
                $.colorbox({inline:true, href:"#login_form_div"});
            }
        else {
            if(editForm && user.isAdminWithServerCheck())
                { error_messages = "Vous devez être admin pour éditer ce point noir"; }
            if(error_messages != "") {
                $('#add_new_description_form__message').text('Erreur! ' + error_messages  + 'Merci.');
                $('#add_new_description_form__message').addClass('errorMessage');
                }
            else {
                entity_string = PlaceInJson(place_data['description'], place_data['lon'],
                    place_data['lat'], place_data['lieu'], place_data['id'], place_data['couleur'],
                    place_data['user_label'], place_data['email'], place_data['user_phonenumber'],place_data['categories']);
                url_edit = Routing.generate('wikipedale_place_change', {_format: 'json'});
                $.ajax({
                    type: "POST",
                    data: {entity: entity_string},
                    url: url_edit,
                    cache: false,
                    success: function(output_json) { 
                        if(! output_json.query.error) { 
                            newPlaceData = output_json.results[0];
                            clear_add_new_description_form();
                            if(user.isRegistered()) {
                                addMarkerWithClickAction(newPlaceData.geom.coordinates[0],
                                    newPlaceData.geom.coordinates[1],
                                    displayPlaceDataFunction,
                                    newPlaceData);
                                $('#add_new_description_form__message').text("Le point noir que vous avez soumis a bien été enregistré. Merci!");
                                setTimeout(
                                    function(){
                                        changingModeFunction();
                                        displayPlaceDataFunction(newPlaceData.id);
                                    },7000);  
                                }
                                else {
                                    $('#add_new_description_form__message').text("Le point noir que vous avez soumis a bien été enregistré. Avant d'afficher le point noir, nous allons vérifier votre adresse mail. Veuillez suivre les instructions qui vous ont été envoyées par email.");
                                    setTimeout(
                                        function(){
                                            changingModeFunction();
                                    },7000); 
                                }
                            $('#add_new_description_form__message').addClass('successMessage');
                        }
                        else { 
                            alert('Mince, il y a un problème. Veuillez nous le signaler. Merci');
                            /*
                            alert(output_json[0].message);
                            alert('ERREUR');*/}
                    },
                    error: function(output_json) {
                        alert('Mince, il y a un problème. Veuillez nous le signaler. Merci');
                        /*
                        alert(JSON.stringify(output_json));
                        alert(output_json.responseText);
                        alert(JSON.parse(output_json.responseText)[0]);
                        alert(JSON.stringify(JSON.parse(output_json.responseText)[0]));
                        alert((output_json.responseText[0]).message);
                        alert('ERREUR'); 
                        */
                    }
                });
            }
        }
    });
}

function clear_add_new_description_form() {
    /** 
    * Clear the data entered in the form with id 'add_new_description_form'
    */
    $("#add_new_description_form input[type=text], #add_new_description_form textarea, #add_new_description_form input[type=hidden]").val("");
    $('#add_new_description_form__message').text("");
    new_placeMarker.display(false);
    reset_add_new_description_form_informer();
}

function changingModeFunction() {
    /**
    * Changin the mode between 'add_new_place' and 'edit_place' / 'show_place'.
    */
    if(!add_new_place_mode) {
        $('.olControlButtonAddPlaceItemActive')
            .each(function(index, value){
                value.innerHTML = 'Annuler';
            })
            .removeClass("buttonPlus")
            .addClass("buttonAnnuler");
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
                    new_placeMarker.display(true);
                    new_placeMarker.lonlat = position;
                    placesLayer.redraw();
                }
            });

            if(user.isRegistered()) {
                $("#div_new_place_form_user_mail").hide();
                }
            else {
                $("#div_new_place_form_user_mail").show();
            }
            $("#add_new_description_div").show();
            $("#div_placeDescription").hide();

            add_new_place_mode = true;
        }
        else {
            $('.olControlButtonAddPlaceItemActive')
                .each(function(index, value){
                    value.innerHTML = 'Ajouter un signalement';
                })
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");

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
                            displayPlaceDataFunction(iid);
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

            $("#add_new_description_div").hide();

            if(last_place_selected != null ) {
                $("#div_placeDescription").show();
            }
            add_new_place_mode = false; 
        }
    };

function displayEmailAndPhoneNumberRegardingToRole() {
    signalement_id = $('#input_place_description_id').val();

    if (signalement_id != "" && signalement_id != undefined) {
        placeData = markers_and_associated_data[signalement_id][1]

        if (user.canVieuwUsersDetails() || user.isAdmin()) {
            $('#span_place_description_signaleur_contact').html('(email : <a href="mailto:'+ placeData.creator.email +'">'+ 
        placeData.creator.email +'</a>, téléphone : '+ placeData.creator.phonenumber + ')');
        }
        else {
            $('#span_place_description_signaleur_contact').text('');
        }
    }
}

function displayRegardingToUserRole() {
    /**
    * if the user has certain role, he can edit certain information
    * this function display or not the button with which we can edit the 
    * information
    */
    if(user.canModifyCategories() || user.isAdmin()) {
        $('#span_place_description_cat_button').show();
    }
    else {
        $('#span_place_description_cat_button').hide();
    }

    if(user.canModifyLittleDetails() || user.isAdmin()) {
        $('#span_place_description_loc_button').show();
        $('#span_place_description_desc_button').show();
    }
    else {
        $('#span_place_description_loc_button').hide();
        $('#span_place_description_desc_button').hide();
    }

    if(user.canModifyPlacetype() || user.isAdmin()) {
        $('#span_place_description_type_button').show();
    }
    else {
        $('#span_place_description_type_button').hide();
    }

    if(user.canModifyManager() || user.isAdmin()) {
        $('#span_place_description_gestionnaire_button').show();
    }
    else {
        $('#span_place_description_gestionnaire_button').hide();
    }

    if(user.canUnpublishADescription() || user.isAdmin()){
        $('#span_place_description_delete_button').show();
    }
    else {
        $('#span_place_description_delete_button').hide();
    }
    if(user.isCeM() || user.isAdmin()){
        $('#span_place_description_commentaireCeM_button').show();
        $('#span_place_description_status_button').show();
    }
    else {
        $('#span_place_description_commentaireCeM_button').hide();
        $('#span_place_description_status_button').hide();
    }


    if(user.isAdmin() || user.isCeM() || user.isGdV()) {
        $('#div_commentaires_cem_gestionnaire').show();

    }
    else{
        $('#div_commentaires_cem_gestionnaire').hide();
    }


    // affichage du commentaire du CeM même si il est vide (afin de pouvoir l'éditer)
    if(user.isCeM() || user.isAdmin()) {
        $('#div_container_place_description_commentaireCeM').show();
    }

    displayEmailAndPhoneNumberRegardingToRole();
}


function displayPlaceDataFunction(id_sig) {
    /**
     * Function which display some data of the place on the webpage.
     executed when the user click on a marker on the index page.
     For this page, a marker represents a place
     * @param {OpenLayers.Marker} placeMarker The marker clicked
     * @param {object} placeData The know data given for the place and receivd from 
     web/app_dev.php/place/list/bycity.json?city=mons
     */
     placeMarker = markers_and_associated_data[id_sig][0];
     placeData =  markers_and_associated_data[id_sig][1];

    if (last_place_selected != null) {
        markers_and_associated_data[last_place_selected][0].setUrl(
            marker_img_url + 'm_' + marker_img_name(markers_and_associated_data[last_place_selected][1].statuses) + '.png'
            );
    }
    placeMarker.setUrl(marker_img_url + 'm_' + marker_img_name(placeData.statuses) + '_selected.png');

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


    if(placeData.moderatorComment != '' || user.isCeM() || user.isAdmin()) {
        $('#span_place_description_commentaireCeM').text(placeData.moderatorComment);
        $('#div_container_place_description_commentaireCeM').show();
    }
    // pas d'affichage du commentaire du CeM si vide
    else {
        $('#span_place_description_commentaireCeM').text('');
        $('#div_container_place_description_commentaireCeM').hide();
    }

    $('#span_place_description_cat').text(categories_list);

    if (placeData.placetype == null){
        $('#span_place_description_type').text("pas encore de type assigné");
    }
    else {
        $('#span_place_description_type').text(placeData.placetype.label);
    }
    if (placeData.manager == null) {
        $('#span_place_description_gestionnaire').text("pas encore de gestionnaire assigné");
    }
    else{
        $('#span_place_description_gestionnaire').text(placeData.manager.label);
    }
    

    $('#span_place_description_status').text(color_trad_text[0]);

    for (i = 0; i < placeData.statuses.length; i++)
        {  
            if (placeData.statuses[i].t == 'cem')
            {
                $('#span_place_description_status').text(color_trad_text[placeData.statuses[i].v]); 
            }
        }

    if (user.canVieuwUsersDetails() || user.isAdmin()) {
        $('#span_place_description_signaleur_contact').html('(email : <a href="mailto:'+ placeData.creator.email +'">'+ 
        placeData.creator.email +'</a>, téléphone : '+ placeData.creator.phonenumber + ')');
    }

    if(user.isGdV() || user.isCeM() || user.isAdmin()){
        updateLastComment(placeData.id);
        updateAllComments(placeData.id);
        $("#span_plus_de_commenaitres_link a").attr("href","javascript:comments_mode(" + placeData.id + ");");
        $("#form_add_new_comment").attr("action","javascript:submitNewCommentForm(" + placeData.id + ");");
    }


    descriptionHideEdit(); // si l'utilisateur a commencé à éditer , il faut cacher les formulaires
    displayRegardingToUserRole();

    $('#div_placeDescription').show();
}