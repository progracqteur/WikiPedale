function update_informer_for_form() {
	var l_value = jQuery('#l').attr('value');
	var d_value = jQuery('#d').attr('value');
	var n_value = jQuery('#n').attr('value');
	var e_value = jQuery('#e').attr('value');
	var all_field_filled = true;

	if (l_value) {
		document.getElementById('form_informer_lieu').src="../img/form_ok.jpg";
	}
	else
	{
		document.getElementById('form_informer_lieu').src="../img/form_not_ok.jpg";
		all_field_filled = false;
	}
	if (d_value) {
		document.getElementById('form_informer_description').src="../img/form_ok.jpg";
	}
	else
	{
		document.getElementById('form_informer_description').src="../img/form_not_ok.jpg";
		all_field_filled = false;
	}
	if (n_value) {
		document.getElementById('form_informer_user_label').src="../img/form_ok.jpg";
	}
	else
	{
		document.getElementById('form_informer_user_label').src="../img/form_not_ok.jpg";
		all_field_filled = false;
	}
	if (e_value) {
		document.getElementById('form_informer_email').src="../img/form_ok.jpg";
	}
	else
	{
		document.getElementById('form_informer_email').src="../img/form_not_ok.jpg";
		all_field_filled = false;
	}
	if (all_field_filled) {
		document.getElementById('form_informer_general_text').src="../img/form_ok.jpg";
	}
	else
	{
		document.getElementById('form_informer_general_text').src="../img/form_not_ok.jpg";
	}
}

function update_informer_map() {
	document.getElementById('form_informer_map').src="../img/form_ok.jpg";
	}
