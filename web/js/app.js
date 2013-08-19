require.config({
    paths: {
        'jQuery': 'lib/jQuery/jquery-1.8.2',
        'OpenLayers': 'lib/OpenLayers/OpenLayers',
        'select2': 'lib/select2-3.3.2/select2',
        'colorbox': 'lib/Colorbox/jquery.colorbox',
    },
    shim: {
        'jQuery': {
            exports: '$'
        },
        'OpenLayers': {
            exports: 'OpenLayers'
        },
        'select2': {
            deps: ['jQuery'],
            exports: 'jQuery'
        },
        'colorbox':{
            deps: ['jQuery'],
            exports: 'jQuery'
        } 
    }
});

require(['jQuery','recent_activities','data_map_glue','informer','markers_filtering','select2','colorbox','description_creating_form','map_display','login','description_text_display','description_edit_form'],
    function($,recent_activities,data_map_glue,informer,markers_filtering,select2,colorbox,description_creating_form,map_display,login,description_text_display,description_edit_form){

    $.ajaxSetup({ cache: false }); // IE save json data in a cache, this line avoids this behavior

    $(document).ready(function(){
        $('a.connexion').colorbox({
            inline:true,
            width:"400px",
            height:"400px",
            onComplete: function(){ $("#login_input_username").focus(); }
        });

        var data_for_init = $('#data_for_init');

        if (data_for_init.length !== 0)
        {
            var city_name = data_for_init.attr('data-city');
            var city_lon = data_for_init.attr('data-lon');
            var city_lat = data_for_init.attr('data-lat');
            var description_selected_id = data_for_init.attr('data-description_selected_id');

            if(typeof description_selected_id === 'undefined') {
                description_selected_id=null;
            }

            recent_activities.filling(city_name,5);
            data_map_glue.init_app(city_name, city_lon, city_lat,description_selected_id);

            $("#optionsAffichageCategories").select2();
            $('#optionsAffichageCategories').select2("disable");

            $("#optionsAffichagePlaceTypes").select2();
            $('#optionsAffichagePlaceTypes').select2("disable");

            $("#optionsAffichageStatusCeM").select2();
            $("#optionsAffichageStatusCeM").select2("disable");

            $('#span_place_description_cat_edit').select2();
            $('#span_place_description_status_edit').select2();
            $('#span_place_description_type_edit').select2();
            $('#span_place_description_gestionnaire_edit').select2();

            $('#add_new_description_form__categories').select2().on("change", function(e) { informer.update_new_description_form('categories'); });

            $('#optionsAffichageCategories').on("change", function(e) { markers_filtering.display_only_markers_with_selected_categories(); });
            $('#optionsAffichagePlaceTypes').on("change", function(e) { markers_filtering.display_only_markers_with_selected_categories(); });
            $('#optionsAffichageStatusCeM').on("change", function(e) { markers_filtering.display_only_markers_with_selected_categories(); });

            $("#div_returnNormalMode").hide();

            //Login
            $("#loginForm").submit(function(e) { e.preventDefault(); login.catch_form(); });

            // Menu
            $("#div_add_new_description_button").click(function(e) { data_map_glue.mode_change(); });
            $("#div_returnNormalMode").click(function(e) { map_display.normal_mode(); });
            $("#buttonOptionsAffichage").click(function(e) { markers_filtering.activate_unactivate(); } );

            // Filtring
            $('input[name=affichage_tous_ou_filtre_statusCeM]').click(function(e) { markers_filtering.change_mode_for('StatusCeM'); } );
            $('input[name=affichage_tous_ou_filtre_categorie]').click(function(e) { markers_filtering.change_mode_for('Categories'); } );

            // Add New Description
            $("#add_new_description_form__user_label").blur(function(e) { informer.update_new_description_form('user_label'); });
            $("#add_new_description_form__email").blur(function(e) { informer.update_new_description_form('email'); });
            $("#add_new_description_form__user_phonenumber").blur(function(e) { informer.update_new_description_form('user_phonenumber'); });
            $("#add_new_description_form__lieu").blur(function(e) { informer.update_new_description_form('lieu'); });
            $("#add_new_description_form__description").blur(function(e) { informer.update_new_description_form('description'); });
            $("#add_new_description_div form").submit(function(e) { e.preventDefault(); description_creating_form.process(); });


            //Place Description Edit
            $("#span_place_description_loc_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('loc'); });
            $("#span_place_description_desc_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('desc'); });
            $("#span_place_description_commentaireCeM_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('commentaireCeM'); });
            $("#span_place_description_cat_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('cat'); });
            $("#span_place_description_status_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('status'); });
            $("#span_place_description_type_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('type'); });
            $("#span_place_description_gestionnaire_button").click(function(e) { e.preventDefault();  description_edit_form.description_edit_or_save('gestionnaire'); });

            $("#span_place_description_delete_button").click(function(e) {e.preventDefault(); data_map_glue.last_description_selected_delete(); });
            $("span_plus_de_commenaitres_link a").click(function(e) { e.preventDefault(); description_text_display.activate_comments_mode(); });

        }
    });
});