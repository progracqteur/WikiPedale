/**
* This module contains all the js relative to the comment system.
*/

define(['jQuery','basic_data_and_functions'], function($, basic_data_and_functions) {
    function update_last(aPlaceId) {
        /**
        * Update display the last posted comment in the div element having #div_last_private_comment_container as id
        * @param {string} aPlaceId is the id of the town selected.
        */
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
            	   $('#div_last_private_comment_container').html(basic_data_and_functions.nl2br(lastComment.text) + '<br> par : ' + lastComment.creator.label);
        	   }
            },
            error: function(data) {
        	   $('#div_last_private_comment_container').html('error');
            }
        });
    };

    function update_all(aPlaceId) {
        /**
        * Update all the comments in the div element having #div_list_private_comment_container as id
        * @param {string} aPlaceId is the id of the town selected.
        */
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
            	   div_content = div_content + basic_data_and_functions.nl2br(aComment.text) + '<br> par : ' + aComment.creator.label + '<br><br>';
                });
			 $('#div_list_private_comment_container').html(div_content);
            },
            error : function(data) {
        	   $('#div_list_private_comment_container').html('error');
            }
        });
    };

    function submit_creation_form(aPlaceId){
        /**
        * To process when the comment creation form is submitted.
        * @param {string} aPlaceId is the id of the town selected.
        */
        var comment_text = $('#form_add_new_comment__text').val();
        if(comment_text == "") {
            $('#form_add_new_comment__message')
                .val("Veuillez entrer votre commentaire") 
                .removeClass("successMessage")
                .addClass("errorMessage");  
        } else {
            entity_string = ret = '{"entity":"comment","placeId":' + JSON.stringify(aPlaceId) + ',"text":' + JSON.stringify(comment_text) + ',"type":"moderator_manager"}';
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
                        update_last(aPlaceId);
                        update_all(aPlaceId);
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
    };

    return {
        update_last: update_last,
        update_all: update_all,
        submit_creation_form: submit_creation_form
    }
});