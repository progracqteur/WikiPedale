var error_for_l_field = false;
var error_for_d_field = false;
var error_for_n_field = false;
var error_for_e_field = false;
var error_for_c_field = false;
var error_for_p_field = false;
var change_informer_fields = false;

function is_mail_valid(anEmail)
{
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	return(reg.test(anEmail));
}

function reset_informer(){
	error_for_l_field = false;
	error_for_d_field = false;
	error_for_n_field = false;
	error_for_e_field = false;
	error_for_c_field = false;
	error_for_p_field = false;
	change_informer_fields = false;
	document.getElementById('form_informer_map').src="../img/verif_rien.png";
	document.getElementById('form_informer_lieu').src="../img/verif_rien.png";
	document.getElementById('form_informer_description').src="../img/verif_rien.png";
	document.getElementById('form_informer_user_label').src="../img/verif_rien.png";
	document.getElementById('form_informer_email').src="../img/verif_rien.png";
	document.getElementById('form_informer_general_text').src="../img/verif_rien.png";
	document.getElementById('form_informer_phone').src="../img/verif_rien.png";

}

function update_p_informer_for_form() {
	var l_value = jQuery('#p').attr('value');
	if (l_value) {
		document.getElementById('form_informer_phone').src="../img/verif_oui.png";
		error_for_l_field = false;
	}
	else
	{
		document.getElementById('form_informer_phone').src="../img/verif_non.png";
		error_for_l_field = true;
	}
	update_informer_fields()
}

function update_l_informer_for_form() {
	var l_value = jQuery('#l').attr('value');
	if (l_value) {
		document.getElementById('form_informer_lieu').src="../img/verif_oui.png";
		error_for_l_field = false;
	}
	else
	{
		document.getElementById('form_informer_lieu').src="../img/verif_non.png";
		error_for_l_field = true;
	}
	update_informer_fields()
}

function update_d_informer_for_form() {
	var l_value = jQuery('#d').attr('value');
	if (l_value) {
		document.getElementById('form_informer_description').src="../img/verif_oui.png";
		error_for_d_field = false;
	}
	else
	{
		document.getElementById('form_informer_description').src="../img/verif_non.png";
		error_for_d_field = true;
	}
	update_informer_fields()
}

function update_c_informer_for_form() {
	var l_value = jQuery('#c').attr('value');
	if (l_value) {
		document.getElementById('form_informer_categories').src="../img/verif_oui.png";
		error_for_c_field = false;
	}
	else
	{
		document.getElementById('form_informer_categories').src="../img/verif_non.png";
		error_for_c_field = true;
	}
	update_informer_fields()
}

function update_n_informer_for_form() {
	var l_value = jQuery('#n').attr('value');
	if (l_value) {
		document.getElementById('form_informer_user_label').src="../img/verif_oui.png";
		error_for_n_field = false;
	}
	else
	{
		document.getElementById('form_informer_user_label').src="../img/verif_non.png";
		error_for_n_field = true;
	}
	update_informer_fields()
}

function update_e_informer_for_form() {
	var l_value = jQuery('#e').attr('value');
	if (l_value && is_mail_valid(l_value)) {
		document.getElementById('form_informer_email').src="../img/verif_oui.png";
		error_for_e_field = false;
	}
	else
	{
		document.getElementById('form_informer_email').src="../img/verif_non.png";
		error_for_e_field = true;
	}
	update_informer_fields()
}

function update_informer_fields(){
	if (error_for_l_field || error_for_d_field || error_for_n_field || error_for_e_field || error_for_c_field || error_for_p_field) {
		document.getElementById('form_informer_general_text').src="../img/verif_non.png";
		change_informer_fields = true;
	}
	else
	{
		if (change_informer_fields)
		{
			document.getElementById('form_informer_general_text').src="../img/verif_oui.png";
		}
	}
}

function update_informer_map_not_ok() {
	document.getElementById('form_informer_map').src="../img/verif_non.png";
	}

function update_informer_map_ok() {
	document.getElementById('form_informer_map').src="../img/verif_oui.png";
	}
