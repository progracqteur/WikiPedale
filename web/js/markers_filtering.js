/**
* To filter the markers displayed in function of places caterogries, places CeM status or place type.
*/

var markers_filtering = function () {
    var selection_mode_activate = false; // true iff  displaying the div "div_options_affichage"

    var filter_activated = new Array();
    filter_activated['Categories'] = false; // true iff  filtering categories
    filter_activated['PlaceTypes'] = false;  // true iff filtering types
    filter_activated['StatusCeM']=false; // true iff filtering CeM Status

    function activate_unactivate() {
        /**
        * When the user wants to activate the filtering mode and then display the filtering form.
        */
        if(selection_mode_activate) {
            $('#buttonOptionsAffichage')
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");
            document.getElementById("div_options_affichage").style.display = "none";
            document.getElementById("buttonOptionsAffichage").innerHTML = "Options d'affichage"; 
            map_display.display_all_markers();
        }
        else {
            $('#buttonOptionsAffichage')
                .removeClass("buttonPlus")
                .addClass("buttonAnnuler");
            document.getElementById("div_options_affichage").style.display = "block";
            document.getElementById("buttonOptionsAffichage").innerHTML = 'Annuler';
            display_only_markers_with_selected_categories();
        }
        selection_mode_activate = ! selection_mode_activate;
    };

    function display_only_markers_with_selected_categories(){
        /**
        * display the markers regarding to the selection made by the user
        */
        var markers_id_to_display_cat = new Array();
        if(filter_activated['Categories']) { 
            $.each($('#optionsAffichageCategories').select2("val"), function(index, id_cat) {
                if (descriptions.get_id_for('Categories',parseInt(id_cat)) != undefined) {
                    markers_id_to_display_cat = markers_id_to_display_cat.concat(descriptions.get_id_for('Categories',parseInt(id_cat)));
                }
            });
        };

        var markers_id_to_display_types = new Array();
        if(filter_activated['PlaceTypes']) { 
            $.each($('#optionsAffichagePlaceTypes').select2("val"), function(index, id_type) {
                if (descriptions.get_id_for('PlaceTypes',parseInt(id_type)) != undefined) {
                    markers_id_to_display_types = markers_id_to_display_types.concat(descriptions.get_id_for('PlaceTypes',parseInt(id_type)));
                }
            });
        };

        var markers_id_to_display_statusCeM = new Array();
        if(filter_activated['StatusCeM']) {
            console.log('StatusCeM Activated');
            $.each($('#optionsAffichageStatusCeM').select2("val"), function(index, id_type) {
                if (descriptions.get_id_for('StatusCeM',parseInt(id_type)) != undefined) {
                    console.log(id_type);
                    console.log('selected')
                    markers_id_to_display_statusCeM = markers_id_to_display_statusCeM.concat(descriptions.get_id_for('StatusCeM',parseInt(id_type)));
                }
            });
            console.log(JSON.stringify(markers_id_to_display_statusCeM))
        };

        // -> ici continuer
        $.each(descriptions.get_all(), function(desc_id, desc_data) {
            desc_id = parseInt(desc_id);
            if (desc_data != undefined) {
                if((filter_activated['Categories'] && $.inArray(desc_id, markers_id_to_display_cat) == -1) || 
                    (filter_activated['PlaceTypes'] && $.inArray(desc_id, markers_id_to_display_types) == -1) ||
                    (filter_activated['StatusCeM'] && $.inArray(desc_id,markers_id_to_display_statusCeM) == -1)) {
                    map_display.undisplay_marker(desc_id);
                }
                else {
                    map_display.display_marker(desc_id);
                }
            }
        });
    };

    function change_mode_for(typesOrCategoriesOrStatusCeM){
        /**
        * to be used when the user activate of the the filtering for categories and for types
        * @param {string }typesOrCategories either 'Placetypes' either 'Categories'
        */
        if(filter_activated[typesOrCategoriesOrStatusCeM]){
            $('#optionsAffichage' + typesOrCategoriesOrStatusCeM).select2("disable");
        } else {
            $('#optionsAffichage' + typesOrCategoriesOrStatusCeM).select2("enable");
        }
        filter_activated[typesOrCategoriesOrStatusCeM] = ! filter_activated[typesOrCategoriesOrStatusCeM];
        display_only_markers_with_selected_categories();
    };

    return {
        activate_unactivate: activate_unactivate,
        display_only_markers_with_selected_categories:display_only_markers_with_selected_categories,
        change_mode_for: change_mode_for,
    }
}();