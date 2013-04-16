/**
* Displaying informer image when the user add a new description
*/

function is_mail_valid(anEmail) {
	/**
	* Returns True/False if the email is valid
	* @param{string} anEmail  the considered email 
	*/
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	return(reg.test(anEmail));
}

function reset_add_new_description_form_informer(){
	/**
	* Reset all the informer images to '../img/verif_rien.png'
	*/
	$('#add_new_description_div img.verif').each(function(index, img_element){
		$(img_element).attr('src', '../img/verif_rien.png');
	});
}

function update_add_new_description_form_informer(field_name) {
	/**
	* Set the informer images of the considered field to '../img/verif_oui.png' if the field is correctly
	* filled or to '../img/verif_non.png' if not
	* @param{string} field_name  the name of the considered field
	*/
	var value = $('#add_new_description_form__' + field_name).attr('value');
	var is_valid = 'oui';

	if(! value || (field_name == 'email' && (! is_mail_valid(value))))
		is_valid = 'non';

	$('#add_new_description_form_informer__' + field_name).attr('src', '../img/verif_' + is_valid + '.png');
}

function update_informer_map_not_ok() {
	/**
	* Set the informer images of 'map' to '../img/verif_non.png'
	*/
	$('#add_new_description_form_informer__map').attr('src', '../img/verif_non.png');
	}

function update_informer_map_ok() {
	/**
	* Set the informer images of 'map' to '../img/verif_oui.png'
	*/
	$('#add_new_description_form_informer__map').attr('src', '../img/verif_oui.png');
	}