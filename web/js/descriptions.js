var descriptions = function () {
	var d = {};

	function update(new_descriptions_in_json) {
		/**
		* Update the local data. 
		* @param {array of descriptions} new_descriptions_in_json is a 
		json object containing all the description
		*/
		$.each(new_descriptions_in_json, function(index, a_description) {
				d[a_description.id] = a_description;
            });
	}

	function get_by_id(an_id) {
		/**
		* Gets the description of id 'an_id'
		@param {int} an_id The relative id.
		*/
		return d[an_id];
	}

	function update_for_id(an_id, a_description){
		/**
		* Update the description with id 'an_id'
		@param {int} an_id The relative id.
		@param {description} a_description 
		*/
		d[an_id] = a_description;
	}

	function get_all() {
		/**
		* Gets all the descriptions
		*/
		return d;
	}

	function erase(desc_id) {
		/**
		* Remove the description with id desc_id
		*/
		delete d[desc_id];
	}
    
    return {
    	update: update,
    	get_by_id: get_by_id,
    	get_all: get_all,
    	update_for_id: update_for_id,
	erase: erase
    };
}();
