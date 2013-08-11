require.config({
    paths: {
        'jQuery': 'lib/jQuery/jquery-1.7.2',
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

require(['jQuery','recent_activities','data_map_glue','informer','markers_filtering','select2','colorbox'],
    function($,recent_activities,data_map_glue,informer,markers_filtering,select2,colorbox){

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


            $("#add_new_description_div form").attr("action","javascript:description_creating_form.process()")

        }
    });
});