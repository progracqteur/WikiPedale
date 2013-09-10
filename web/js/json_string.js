define(['map_display','user','OpenLayers'], function(map_display,user,OpenLayers) {
    function unregister_user(label,email,phonenumber){
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

    function point(lon,lat){
        /**
        * Returns a json string describing a point.
        * @param{string} lon The longitude of the point.
        * @param{string} lat} The latitude of the point.
        */
        p = new OpenLayers.Geometry.Point(lon, lat);
        p.transform(map_display.get_map().getProjectionObject(), new OpenLayers.Projection('EPSG:4326'));
        parser = new OpenLayers.Format.GeoJSON();
        return parser.write(p, false);
    }

    function change_place(id, changement){
        /**
        * Returns a json string describing a place.
        * @param{int} id The id of the description.
        * @param{string} changement A json string representing the changement to do.
        */
        ret = '{"entity":"place"';
        ret = ret + ',"id":' + JSON.stringify(id) + ',';
        ret = ret + changement;
        return ret + '}';   
    }

    function edit_moderator_comment(id,new_moderator_comment){
        /**
        * Returns a json for editing the moderator comment of a place.
        * @param{int} id The id of the description.
        * @param{string} new_moderator_comment The new moderator comment.
        */
        return change_place(id,'"moderatorComment":' + JSON.stringify(new_moderator_comment));
    }

    function edit_description(id,new_description){
        /**
        * Returns a json for editing the parameter 'description' of a place.
        * @param{int} id The id of the description.
        * @param{string} new_description The new value of the parameter 'description'.
        */
        return change_place(id,'"description":' + JSON.stringify(new_description));
    }

    function edit_location(id,new_location){
        /**
        * Returns a json for editing location of a place.
        * @param{int} id The id of the description.
        * @param{string} new_location The new location.
        */
        return change_place(id,'"addressParts":{"entity":"address","road":' + JSON.stringify(new_location) + '}');
    }

    function edit_category(id, new_category_id){
        /**
        * Returns a json for editing the category (single) of a place.
        * @param{int} id The id of the description.
        * @param{int} new_category_id The new category id.
        */
        return change_place(id,'"categories":[{"entity":"category","id":' + new_category_id + '}]');
    }

    function edit_categories(id, new_categories_id){
        /**
        * Returns a json for editing the categories of a place.
        * @param{int} id The id of the description.
        * @param{int array} new_categories_id The new categories id.
        */
        categories_desc = '"categories":[';
        for (var i = 0; i < new_categories_id.length; i++) { 
            categories_desc = categories_desc + '{"entity":"category","id":' + new_categories_id[i] + '}';
            if (i < (new_categories_id.length - 1)) {
                categories_desc = categories_desc + ',';
            }
        }
        categories_desc = categories_desc + ']';
        return change_place(id,categories_desc);
    }

    function edit_status(id,status_type,new_status_value){
        /**
        * Returns a json for editing the status of a place.
        * @param{int} id The id of the description.
        * @param{string} status_type The type of the status
        * @param{string} new_status_value The new value of the status.
        */
        return change_place(id,'"statuses":[{"t":"' + status_type + '","v":"' + new_status_value + '"}]');
    }

    function edit_manager(id,new_manager_id){
        /**
        * Returns a json for editing the manager of a place.
        * @param{int} id The id of the description.
        * @param{int} new_manager_id The id of the new manager.
        */
        return change_place(id,'"manager": {"entity":"group","type":"MANAGER","id":' 
            + JSON.stringify(new_manager_id)  + '}');
    }

    function edit_place_type(id, new_placetype_id){
        /**
        * Returns a json for editing place type of a place.
        * @param{int} id The id of the description.
        * @param{int} new_placetype_id The new id of the place type.
        */
        return change_place(id,'"placetype":{"id":' +  JSON.stringify(new_placetype_id) + ',"entity":"placetype"}');
    }


    function edit_place_position(id,lon,lat) {
        /**
        * Returns a json for editing the position of a place.
        * @param{int} id The id of the description.
        * @param{int} lon the new longitude of the place.
        * @param{int} lat the new latitude of the place.
        */
        return change_place(id,'"geom":'+ point(lon,lat));
    }



    function delete_place(id){
        /**
        * Returns a json for deleting a description.
        * @param{int} id The id of the description to delete.
        */
        return change_place(id,'"accepted":false');
    }

    function edit_place(description, lon, lat, address, id, color, user_label, user_email, user_phonenumber, categories) {
        /**
        * Returns a json string used for adding/editing a new description.
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

        if(id==undefined || id==null) {
            ret = ret + ',"id":null';
        } else { 
            ret = ret + ',"id":' + JSON.stringify(id);
        }

        if(lon!=undefined && lon!=null && lat!=undefined && lon!=null) {
            ret = ret + ',"geom":'+ point(lon,lat);
        }

        if( !user.isRegistered() && (user_label != undefined || user_email != undefined)) {
            ret = ret + ',"creator":' + unregister_user(user_label, user_email, user_phonenumber);
        }

        ret = ret + ',"description":' + JSON.stringify(description)
            + ',"addressParts":{"entity":"address","road":' + JSON.stringify(address) + '}'

        ret = ret + ',"categories":[';
        for (var i = 0; i < categories.length; i++) {
            ret = ret + '{"entity":"category","id":' + categories[i] + '}';
            if (i < (categories.length - 1)) {
                ret = ret + ',';
            }
        }
        ret = ret + ']';
        return ret + '}';
    }
    return {
        unregister_user: unregister_user,
        change_place: change_place,
        edit_moderator_comment: edit_moderator_comment,
        edit_description: edit_description,
        edit_location: edit_location,
        edit_category: edit_category,
        edit_categories: edit_categories,
        edit_status: edit_status,
        edit_manager: edit_manager,        
        edit_place_type: edit_place_type,
        delete_place: delete_place,
        edit_place: edit_place,
        edit_place_position: edit_place_position
    }
});