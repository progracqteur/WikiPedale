var baseUrlsplit = Routing.getBaseUrl().split('/');
var web_dir = '';
var i = 0;
for (i = 0; i < (baseUrlsplit.length - 1);  i++)
{
    web_dir = web_dir + baseUrlsplit[i] + '/';
} 

//var colors_in_marker = 1; //number of color in a marker
var c1_label = "cem";
var c2_label;
var c3_label;


var add_new_place_mode = false; // true when the user is in a mode for adding new place

var id_markers_for = new Array();
id_markers_for['Categories'] = new Array();
id_markers_for['PlaceTypes'] = new Array();
id_markers_for['StatusCeM'] = new Array();

var new_placeMarker;

var townId = null;


// marker with color
var color_trad = new Array();
color_trad['0'] = 'w';
color_trad['-1'] = 'd';
color_trad['1'] = 'r';
color_trad['2'] = 'o';
color_trad['3'] = 'g';

function update_markers_and_associated_data(){
    // removing the information
    descriptions.erase_all();

    jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
            descriptions.update(data.results,null);
        },
        complete: function(data) {
            signalement_id = $('#input_place_description_id').val();
            if (signalement_id != "" && signalement_id != undefined) {
                // be sure that a place is selected
                description_text_display.display_regarding_to_user_role();
            }

            console.log('update the markers');
        }
    });
};


function nl2br (str, is_xhtml) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Philip Peterson
  // +   improved by: Onno Marsman
  // +   improved by: Atli Þór
  // +   bugfixed by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Maximusya
  // *     example 1: nl2br('Kevin\nvan\nZonneveld');
  // *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
  // *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
  // *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
  // *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
  // *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'
  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function is_mail_valid(anEmail) {
  /**
  * Returns True/False if the email is valid
  * @param{string} anEmail  the considered email 
  */
  var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
  return(reg.test(anEmail));
}
