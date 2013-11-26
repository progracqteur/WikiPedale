"use strict";
/**
* To filter the markers displayed in function of places caterogries, places CeM status or place type.
* This module is used to display the markers regarding to the filtering options.
*/

define(['jQuery','map_display','descriptions','description'], function($,map_display,descriptions,description) {
    var filtering_form_activated = false,  // true iff  displaying the div "div_options_affichage" and
    // the choice of the user (done in the filtering form) has to be considered.
        mode_activated = new Array(); // to remember each option of the filtering form has been
    // choosed by the user

    mode_activated['FilterCategories'] = false; // true iff  filtering categories
    mode_activated['AddLongTermCategories'] = false; // true iff  adding (with filtering) signalement with PN Categories (ie Categories with long term)
    mode_activated['FilterStatusCeM']=false; // true iff filtering CeM Status
    mode_activated['AddStatusCeMRejete'] = false; // true iff  adding signalements with CeM Status Rejected

    function activate_unactivate_filtering_form() {
        /**
        * Function used to signal that the user wants to activate/unactivate the filtering mode.
        * This function will display/undisplay the filtering form.
        */
        if(filtering_form_activated) {
            $('#buttonOptionsAffichage')
                .removeClass("buttonAnnuler")
                .addClass("buttonPlus");
            document.getElementById("div_options_affichage").style.display = "none";
            document.getElementById("buttonOptionsAffichage").innerHTML = "Options d'affichage"; 
            
        }
        else {
            $('#buttonOptionsAffichage')
                .removeClass("buttonPlus")
                .addClass("buttonAnnuler");
            document.getElementById("div_options_affichage").style.display = "block";
            document.getElementById("buttonOptionsAffichage").innerHTML = 'Annuler';
        }
        filtering_form_activated = ! filtering_form_activated;
        display_markers_regarding_to_filtering();
    }

    function display_markers_regarding_to_filtering(){
        /**
        * Display on the map the markers regarding to the selection made by the user
        * via the filtering form (or not if not activated).
        */
        var id_cat_to_display = [], //the id of the categories that will be displayed on the map
            statusCeM_to_display = []; //the statusCeM that will be displayed on the map

        // Short term and medium categories
        if(mode_activated['FilterCategories'] && filtering_form_activated) {
            $.each($('#optionsAffichageFilterCategories').select2("val"), function(index, id_cat) {
                id_cat_to_display.push(parseInt(id_cat));
            });
        } else {
            $('#optionsAffichageFilterCategories option').each(
                function(i,v) { id_cat_to_display.push(parseInt(v.value)); });
        }

        // Long term categories
        if(mode_activated['AddLongTermCategories']) {
            $.each($('#optionsAffichageAddLongTermCategories').select2("val"), function(index, id_cat) {
                id_cat_to_display.push(parseInt(id_cat));
            });
        }

        // White, Red, Yellow, Green statuses
        if(mode_activated['FilterStatusCeM'] && filtering_form_activated) {
            $.each($('#optionsAffichageFilterStatusCeM').select2("val"), function(index, id_type) {
                statusCeM_to_display.push(parseInt(id_type));
            });
        }
        else {
            $('#optionsAffichageFilterStatusCeM option').each( function(i,v) {
                statusCeM_to_display.push(parseInt(v.value)); });
        }

        // Gray (rejected) status
        if(mode_activated['AddStatusCeMRejete']) {
            statusCeM_to_display.push(-1);  
        }

        $.each(descriptions.get_all(), function(desc_id, desc_data) {
            if (typeof(desc_data) !== undefined) {
                // desc_data does not have a status of type cem it has to be considered as 0 (not considered)
                if(statusCeM_to_display.indexOf(parseInt(description.get_status('cem', desc_data, 0))) !== -1 &&
                    id_cat_to_display.indexOf(parseInt(description.get_category_id(desc_data,-1))) !== -1) {
                    map_display.display_marker(desc_id);
                }
                else {
                    map_display.undisplay_marker(desc_id);
                }
            }
        });
    }

    function change_mode_for(filtering_option){
        /**
        * To be used when the user activate/unactivate a filtering option in the filtering mode.
        * @param {string } typesOrCategories either 'Placetypes' either 'Categories'
        */
        if(mode_activated[filtering_option]){
            $('#optionsAffichage' + filtering_option).select2("disable");
        } else {
            $('#optionsAffichage' + filtering_option).select2("enable");
        }
        mode_activated[filtering_option] = ! mode_activated[filtering_option];
        display_markers_regarding_to_filtering();
    }

    return {
        activate_unactivate_filtering_form: activate_unactivate_filtering_form,
        display_markers_regarding_to_filtering:display_markers_regarding_to_filtering,
        change_mode_for: change_mode_for
    };
});