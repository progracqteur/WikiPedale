var recent_activities = function () {
	function filling(aCitySlug,nbr_max){
        jsonUrlData  =  Routing.generate('wikipedale_history_place_by_city', {_format: 'json', citySlug: aCitySlug, max:nbr_max});
        $.ajax({
            dataType: "json",
            url: jsonUrlData,
            success: function(data) {
                $.each(data.results, function(index, aLastModif) {
                    $('#div_content_dernieres_modifs').append(aLastModif.text);
                    var lien_voir = $(document.createElement('a'))
                        .text('(voir)')
                        .attr("href", "?id=" + aLastModif.placeId);
                    $('#div_content_dernieres_modifs').append(lien_voir);
                    $('#div_content_dernieres_modifs').append('<br>');
                });
            }
        });
    }

    return {
    	filling: filling
    }
}();