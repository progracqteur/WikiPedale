/**
* This module contains all the functions relative to the photos.
*/

define(['jQuery','basic_data_and_functions'], function($,basic_data_and_functions) {
    function pop_up_add_photo(i) {
        /**
        * Open a new window where it is possible to add a photo.
        * @param {int} i The id of the description  for which the photo will be associated 
        */
        window.open(Routing.generate('wikipedale_photo_new', {_format: 'html', placeId: i}));
    }

    function refresh_span_photo(id) {
        /**
        * Refresh the div span_photo element and display in it the photos associated to a description.
        * @param {int} id The id of the description relative to the photos.
        */
        url_photo_list = Routing.generate('wikipedale_photo_list_by_place', {_format: 'json', placeId: id});
        $.getJSON(url_photo_list, function(raw_data) {
            data = raw_data.results;
            if(data.length == 0) {
                $('.span_photo').each(function() { this.innerHTML = '<img src="' + basic_data_and_functions.web_dir + 'img/NoPictureYet.png" />'; });
            } else {
                span_photo_inner = '<br />';
                $.each(data, function(i,row) {
                    span_photo_inner +=  ' <a target="_blank" href="' + basic_data_and_functions.web_dir + row.webPath + '"><image src="' + basic_data_and_functions.web_dir + row.webPath + '"></image></a>';
                    $('.span_photo').each(function() { this.innerHTML = span_photo_inner; });
                })
            }
        });
    }

    return {
        pop_up_add_photo: pop_up_add_photo,
        refresh_span_photo: refresh_span_photo
    }
});