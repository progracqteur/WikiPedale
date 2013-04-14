function updateLastComment(aPlaceId){
	jsonUrlData  =  Routing.generate('wikiedale_comment_last_by_place', {_format: 'json', placeId: aPlaceId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
        	console.log(data.results);
        	console.log(data.results.length);
        	if (data.results.length == 0) {
        		$('#div_last_private_comment_container').html('pas encore de commentaire pour ce signalement');
        	}
        	else {
        		lastComment = data.results[0];
            	$('#div_last_private_comment_container').html(lastComment.text + '<br> par : ' + lastComment.creator.label);
        	}
        },
        error: function(data) {
        	$('#div_last_private_comment_container').html('error');
        }
    });
}

function updateAllComments(aPlaceId){
	jsonUrlData  =  Routing.generate('wikiedale_comment_list_by_place', {_format: 'json', placeId: aPlaceId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
        	var div_content = ""
        	if (data.results.length == 0) {
        		div_content = 'pas encore de commentaire pour ce signalement';
        	}
            $.each(data.results, function(index, aComment) {
            	div_content = div_content + aComment.text + '<br> par : ' + aComment.creator.label + '<br><br>';
            });
			$('#div_list_private_comment_container').html(div_content);
        },
        error : function(data) {
        	$('#div_list_private_comment_container').html('error');
        }
    });
}