/**
* All the descriptions data given by the server and stored in JS.
*/

// mettre des parseInt

var descriptions = function () {
	var d, id_for;

    function init() {
    	/**
    	* Initialize the data in the descriptions
    	*/ 
    	d = {};
    	id_for = {}; // tab with all the ids of description having that categories/placetypes/statusCeM.
		id_for['Categories'] = {};
		id_for['PlaceTypes'] = {};
		id_for['StatusCeM'] = {};
    	id_for['StatusCeM']["0"] = new Array();
    }


	function update(new_descriptions_in_json, do_after_update) {

		/**
		* Updates the local data. d[[]]
		* @param {array of descriptions} new_descriptions_in_json It is a 
		* @param {function} a function to be executed after updating the descriptions. can be null if not execute
		json object containing all the description
		*/
		$.when(
			$.each(new_descriptions_in_json, 
				function(index, a_description) {
					single_update(a_description);
        	    }
        	)
		).done( function() {
			if (do_after_update) {
				do_after_update();
			}
		});
	}

	function single_update(a_description){
		/**
		* Update a description
		* @param {a json descriptions} a_description The description to be updated. it is a
		json object containing all the information about the description
		*/
		desc_id = parseInt(a_description.id)

		if (d[desc_id]) { // removing all the information about this description in id_for
			erase_id_for_data_relative_to(d[desc_id]);
		}

        // Then adding the id in id_for regarding to the new_description
        $.each(a_description.categories, function(i, c) {
        	if (id_for['Categories'][parseInt(c.id)] == undefined){
            	id_for['Categories'][parseInt(c.id)] = new Array();
        	}
        	id_for['Categories'][parseInt(c.id)].push(desc_id);
    	});

        if (a_description.placetype != null) {
	        if(id_for['PlaceTypes'][parseInt(a_description.placetype.id)] == undefined) {
            	id_for['PlaceTypes'][parseInt(a_description.placetype.id)] = new Array();
        	}
        	id_for['PlaceTypes'][parseInt(a_description.placetype.id)].push(desc_id);
    	}

	    var a_description_id_added_for_cem = false;
    	$.each(a_description.statuses, function(index, type_value) {
        	if(type_value.t == "cem") {
            	if(id_for['StatusCeM'][type_value.v.toString()] == undefined) {
            		id_for['StatusCeM'][type_value.v.toString()] = new Array();
            	}
            	id_for['StatusCeM'][type_value.v.toString()].push(desc_id);
            	a_description_id_added_for_cem = true;
        	}
    	});
    	if(! a_description_id_added_for_cem) {
        	id_for['StatusCeM']["0"].push(desc_id);
    	}

    	d[desc_id] = a_description;
	} 

	function get_by_id(an_id) {
		/**
		* Gets the description of id 'an_id'
		@param {int} an_id The relative id.
		*/
		return d[parseInt(an_id)];
	}

	function get_all() {
		/**
		* Gets all the descriptions
		*/
		return d;
	}

	function erase_id_for_data_relative_to(a_description) {
		/**
		* Erases the data in the id_for variable relative to the description a_description
		* @param {a json description} a_description The description for which erasing the data.
		*/
		desc_id = parseInt(a_description.id)

		$.each(a_description.categories, function(i,c) {
			index_sig = id_for['Categories'][parseInt(c.id)].indexOf(desc_id);
            id_for['Categories'][parseInt(c.id)].splice(index_sig,1);
        });

        $.each(a_description.statuses, function(i, stat) {
    	    if(stat.t == "cem") {
        	    index_sig = id_for['StatusCeM'][stat.v].indexOf(desc_id);
           	    id_for['StatusCeM'][stat.v].splice(index_sig,1);
            }
        });
        if (a_description.placetype != null) {   
            index_sig = id_for['PlaceTypes'][parseInt(a_description.placetype.id)].indexOf(desc_id);
            id_for['PlaceTypes'][parseInt(a_description.placetype.id)].splice(index_sig,1);
        }
	}

	function erase_all(){
		/**
		* Remove all the descriptions
		*/
		init();
	}

	function erase(desc_id) {
		/**
		* Remove the description with id desc_id
		* @param {int} dest_id The id of the description
		*/
		desc_id = parseInt(desc_id);
		erase_id_for_data_relative_to(d[desc_id]);
		delete d[desc_id];
	}

	function get_id_for(cst,a_cst_id) {
		/**
		* Return a list of the description id such that it has the categorie/stautsCeM/type
		* asked
		* @param {string}  cst is 'Categories', 'StatusCeM' or 'PlaceTypes'
		* @param {int} the id of the cst
		*/
		return id_for[cst][parseInt(a_cst_id)];
	}

	init();
    return {
    	update: update,
    	single_update: single_update,
    	get_by_id: get_by_id,
    	get_all: get_all,
    	single_update: single_update,
    	get_id_for: get_id_for,
    };
}();
