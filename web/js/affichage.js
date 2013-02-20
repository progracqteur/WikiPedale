display_options = false;

function action_buttonOptionsAffichage() {
    if(display_options) {
        document.getElementById("div_options_affichage").style.display = "none";
        document.getElementById("buttonOptionsAffichage").innerHTML = "Options d'affichage";
        
    }
    else {
        document.getElementById("div_options_affichage").style.display = "block";
        document.getElementById("buttonOptionsAffichage").innerHTML = 'Annuler';
    }
    display_options = ! display_options;
};

function action_buttonFilter(){
	$('#optionsAffichageCategories').select2("enable");
	console.log('blop');
};

function action_buttonNoFilter(){
	$('#optionsAffichageCategories').select2("disable");
	console.log('blop');
};