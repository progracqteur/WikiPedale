/**
* Displaying informer image when the user add a new description
*/
define(['jQuery','basic_data_and_functions'], function($,basic_data_and_functions) {
	function reset_new_description_form(){
		/**
		* Reset all the informer images to '../img/verif_rien.png'
		*/
		$('#add_new_description_div img.verif').each(function(index, img_element){
			$(img_element).attr('src', basic_data_and_functions.web_dir + 'img/verif_rien.png');
		});
	}

	function update_new_description_form(field_name) {
		/**
		* Set the informer images of the considered field to '../img/verif_oui.png' if the field is correctly
		* filled or to '../img/verif_non.png' if not
		* @param{string} field_name  the name of the considered field
		*/
		var value = $('#add_new_description_form__' + field_name).attr('value');
		var is_valid = 'oui';

		if(! value || (field_name == 'email' && (! basic_data_and_functions.is_mail_valid(value))))
			is_valid = 'non';

		$('#add_new_description_form_informer__' + field_name).attr('src', basic_data_and_functions.web_dir +  'img/verif_' + is_valid + '.png');
	}

	function map_not_ok() {
		/**
		* Set the informer images of 'map' to '../img/verif_non.png'
		*/
		$('#add_new_description_form_informer__map').attr('src', basic_data_and_functions.web_dir + 'img/verif_non.png');
	}

	function map_ok() {
		/**
		* Set the informer images of 'map' to '../img/verif_oui.png'
		*/
		$('#add_new_description_form_informer__map').attr('src', basic_data_and_functions.web_dir + 'img/verif_oui.png');
	}

	return {
		reset_new_description_form: reset_new_description_form,
		update_new_description_form: update_new_description_form,
		map_not_ok: map_not_ok,
		map_ok: map_ok,
	};
});