// PHOTO
function pop_up_add_photo(i) {
    window.open(Routing.generate('wikipedale_photo_new', {_format: 'html', placeId: i}));
}

function refresh_span_photo(id) {
    url_photo_list = Routing.generate('wikipedale_photo_list_by_place', {_format: 'json', placeId: id});
    $.getJSON(url_photo_list, function(raw_data) {
    data = raw_data.results;
    if(data.length == 0) {
        $('.span_photo').each(function() { this.innerHTML = '<img src="../img/NoPictureYet.png" />'; });
        }
    else {
        span_photo_inner = '<br />';
        $.each(data, function(i,row) {
        span_photo_inner +=  ' <a target="_blank" href="' + web_dir + row.webPath + '"><image src="' + web_dir + row.webPath + '"></image></a>';
        $('.span_photo').each(function() { this.innerHTML = span_photo_inner; });
        })
        }
    });
}