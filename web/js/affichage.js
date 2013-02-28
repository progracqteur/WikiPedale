var display_option_affichage = false; // true iff  displaying the div "div_options_affichage"

var filter_selected = new Array();
filter_selected['Categories'] = false; // true iff  filtering categories
filter_selected['PlaceTypes'] = false;  // true iff filtering types

function action_buttonOptionsAffichage() {
    /**
    * to be used when the user ask to display or not the div "div_options_affichage"
    */
    if(display_option_affichage) {
        document.getElementById("div_options_affichage").style.display = "none";
        document.getElementById("buttonOptionsAffichage").innerHTML = "Options d'affichage";  
        display_all_markers();
    }
    else {
        document.getElementById("div_options_affichage").style.display = "block";
        document.getElementById("buttonOptionsAffichage").innerHTML = 'Annuler';
        display_only_markers_with_selected_categories();
    }
    display_option_affichage = ! display_option_affichage;
};

function display_all_markers(){
    /**
    * display all the markers on the map
    */
    $.each(markers_and_associated_data, function(marker_id, marker_data) {
        if (marker_data != undefined) {
            marker_data[0].display(true);
        }
    });
}

function display_only_markers_with_selected_categories(){
    /**
    * display the markers regarding to the selection made by the user
    */
    markers_id_to_display_cat = new Array();
    if(filter_selected['Categories']) { 
        $.each($('#optionsAffichageCategories').select2("val"), function(index, id_cat) {
            if (id_markers_for['Categories'][parseInt(id_cat)] != undefined) {
                markers_id_to_display_cat = markers_id_to_display_cat.concat(id_markers_for['Categories'][parseInt(id_cat)]);
            }
        });
    };

    markers_id_to_display_types = new Array();
    if(filter_selected['PlaceTypes']) { 
        $.each($('#optionsAffichagePlaceTypes').select2("val"), function(index, id_type) {
            if (id_markers_for['PlaceTypes'][parseInt(id_type)] != undefined) {
                markers_id_to_display_types = markers_id_to_display_types.concat(id_markers_for['PlaceTypes'][parseInt(id_type)]);
            }
        });
    };

    $.each(markers_and_associated_data, function(marker_id, marker_data) {
        if (marker_data != undefined) {
            if((filter_selected['Categories'] && markers_id_to_display_cat.indexOf(marker_id) == -1) || 
                (filter_selected['PlaceTypes'] && markers_id_to_display_types.indexOf(marker_id) == -1)) {
                marker_data[0].display(false);
            }
            else {
                marker_data[0].display(true);
            }
        }
    });
};

function changeFilteringMode(typesOrCategories){
    /**
    * to be used when the user activate of the the filtering for categories and for types
    * @param {string }typesOrCategories either 'Placetypes' either 'Categories'
    */
    if(filter_selected[typesOrCategories]){
        $('#optionsAffichage' + typesOrCategories).select2("disable");
    }
	else {
        $('#optionsAffichage' + typesOrCategories).select2("enable");
    }
    filter_selected[typesOrCategories] = ! filter_selected[typesOrCategories];
    display_only_markers_with_selected_categories();
};