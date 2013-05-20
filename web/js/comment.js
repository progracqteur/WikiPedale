function updateLastComment(aPlaceId){
	jsonUrlData  =  Routing.generate('wikiedale_comment_last_by_place', {_format: 'json', placeId: aPlaceId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
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

function submitNewCommentForm(aPlaceId){
    var comment_text = $('#form_add_new_comment__text').val();
    if(comment_text == "") {
        $('#form_add_new_comment__message')
            .val("Veuillez entrer votre commentaire") 
            .removeClass("successMessage")
            .addClass("errorMessage");  
    }
    else {
        entity_string = ret = '{"entity":"comment","placeId":' + JSON.stringify(aPlaceId) + ',"text":' + JSON.stringify(comment_text) + ',"type":"moderator_manager"}';
        console.log(entity_string);
        $.ajax({
            type: "POST",
            data: {entity: entity_string},
            url: Routing.generate('wikipedale_comment_change', {_format: 'json'}),
            cache: false,
            success: function(output_json) { 
                if(output_json.query.error != undefined && ! output_json.query.error) { 
                    $('#form_add_new_comment__message')
                        .val("Votre commentaire a été ajouté. Merci.")
                        .removeClass("errorMessage")
                        .addClass("successMessage");
                    updateLastComment(aPlaceId);
                    updateAllComments(aPlaceId);
                }
                else { 
                    $('#form_add_new_comment__message')
                        .val("Une erreur s'est produite. Veuillez réessayer ou nous avertir. Merci.")
                        .removeClass("successMessage")
                        .addClass("errorMessage");  
                }
            },
            error: function(output_json) {
                $('#form_add_new_comment__message')
                    .val("Une erreur s'est produite. Veuillez réessayer ou nous avertir. Merci.")
                    .removeClass("successMessage")
                    .addClass("errorMessage");  
            }
        });
    }
}