display_option_affichage = false; // display or not the div div_options_affichage

filter_categories_selected = false;
filter_types_selected = false;

function changingFilterCategoriesMode() { }

function action_buttonOptionsAffichage() {
    /**
    *
    */
    if(display_option_affichage) {
        document.getElementById("div_options_affichage").style.display = "none";
        document.getElementById("buttonOptionsAffichage").innerHTML = "Options d'affichage";  
    }
    else {
        document.getElementById("div_options_affichage").style.display = "block";
        document.getElementById("buttonOptionsAffichage").innerHTML = 'Annuler';
    }
    display_option_affichage = ! display_option_affichage;
};

function only_display_marker_with_selected_categories(){
    $.each(markers_and_associated_data, function(index, marker_data) {
        if (marker_data != undefined) {
            marker_data[0].display(false);
        }
    });
    $.each($('#optionsAffichageCategories').select2("val"), function(index, id_cat) {
        if (categories_and_id_markers[parseInt(id_cat)] != undefined)
        {
            $.each(categories_and_id_markers[parseInt(id_cat)], function(index, id_marker_to_display) {
                markers_and_associated_data[id_marker_to_display][0].display(true);
            });
        }
    });
};

function action_buttonFilter(){
    if(filter_categories_selected){
        $('#optionsAffichageCategories').select2("disable");
        $.each(markers_and_associated_data, function(index, marker_data) {
            if (marker_data != undefined) {
                marker_data[0].display(true);
            }
        });
    }
	else {
        $('#optionsAffichageCategories').select2("enable");
        only_display_marker_with_selected_categories();
    }

    filter_categories_selected  = ! filter_categories_selected;
};