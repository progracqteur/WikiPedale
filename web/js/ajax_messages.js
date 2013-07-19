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
    p.transform(map_display.map.getProjectionObject(), new OpenLayers.Projection('EPSG:4326'));
    parser = new OpenLayers.Format.GeoJSON();
    return parser.write(p, false);
}

function PlaceInJSonWithOtherNameValue(id, otherNameValue){
    ret = '{"entity":"place"';
    ret = ret + ',"id":' + JSON.stringify(id);
    ret = ret + otherNameValue;
    return ret + '}';   
}

function EditDescriptionCommentaireCeMInJson(id,description){
    return PlaceInJSonWithOtherNameValue(id,',"moderatorComment":' + JSON.stringify(description));
}

function EditDescriptionDescInJson(id,description){
    return PlaceInJSonWithOtherNameValue(id,',"description":' + JSON.stringify(description));
}

function EditDescriptionLocInJson(id,loc){
    return PlaceInJSonWithOtherNameValue(id,',"addressParts":{"entity":"address","road":' + JSON.stringify(loc) + '}');
}

function EditDescriptionSingleCatInJson(id,cat){
    categories_desc = ',"categories":[';
    categories_desc = categories_desc + '{"entity":"category","id":' + cat + '}';
    categories_desc = categories_desc + ']';
    return PlaceInJSonWithOtherNameValue(id,categories_desc);
}

function EditDescriptionCatInJson(id,cat){
    categories_desc = ',"categories":[';
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

function EditDescriptionGestionnaireInJson(id,gestionnaire_id){
    return PlaceInJSonWithOtherNameValue(id,',"manager": {"entity":"group","type":"MANAGER","id":' 
        + JSON.stringify(gestionnaire_id)  + '}');
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
    if( !user.isRegistered() && (user_label != undefined || user_email != undefined))
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