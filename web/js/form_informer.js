var error_for_l_field = false;
var error_for_d_field = false;
var error_for_n_field = false;
var error_for_e_field = false;
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
	change_informer_fields = false;
}

function update_l_informer_for_form() {
	var l_value = jQuery('#l').attr('value');
	if (l_value) {
		document.getElementById('form_informer_lieu').src="../img/form_ok.jpg";
		error_for_l_field = false;
	}
	else
	{
		document.getElementById('form_informer_lieu').src="../img/form_not_ok.jpg";
		error_for_l_field = true;
	}
	update_informer_fields()
}

function update_d_informer_for_form() {
	var l_value = jQuery('#d').attr('value');
	if (l_value) {
		document.getElementById('form_informer_description').src="../img/form_ok.jpg";
		error_for_d_field = false;
	}
	else
	{
		document.getElementById('form_informer_description').src="../img/form_not_ok.jpg";
		error_for_d_field = true;
	}
	update_informer_fields()
}

function update_n_informer_for_form() {
	var l_value = jQuery('#n').attr('value');
	if (l_value) {
		document.getElementById('form_informer_user_label').src="../img/form_ok.jpg";
		error_for_n_field = false;
	}
	else
	{
		document.getElementById('form_informer_user_label').src="../img/form_not_ok.jpg";
		error_for_n_field = true;
	}
	update_informer_fields()
}

function update_e_informer_for_form() {
	var l_value = jQuery('#e').attr('value');
	if (l_value && is_mail_valid(l_value)) {
		document.getElementById('form_informer_email').src="../img/form_ok.jpg";
		error_for_e_field = false;
	}
	else
	{
		document.getElementById('form_informer_email').src="../img/form_not_ok.jpg";
		error_for_e_field = true;
	}
	update_informer_fields()
}

function update_informer_fields(){
	if (error_for_l_field || error_for_d_field || error_for_n_field || error_for_e_field) {
		document.getElementById('form_informer_general_text').src="../img/form_not_ok.jpg";
		change_informer_fields = true;
	}
	else
	{
		if (change_informer_fields)
		{
			document.getElementById('form_informer_general_text').src="../img/form_ok.jpg";
		}
	}
}

function update_informer_map() {
	document.getElementById('form_informer_map').src="../img/form_ok.jpg";
	}
