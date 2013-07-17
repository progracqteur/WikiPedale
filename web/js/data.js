var displaying_tiny_map = false; // Display or not a tiny Map
var old_center; // To re-center the map after displaying the tiny map

var map; // Variable to acces to the map
var osmLayer; // OSM layer
var placesLayer; // layer where existing places are drawing
var new_placeLayer;  // layer where the user can draw a new place
var zoom_map = 13; // zoom level of the map

var baseUrlsplit = Routing.getBaseUrl().split('/');
var web_dir = '';
var i = 0;
for (i = 0; i < (baseUrlsplit.length - 1);  i++)
{
    web_dir = web_dir + baseUrlsplit[i] + '/';
} 
var marker_img_url = web_dir + 'OpenLayers/img/'; // where is the dir containing the OpenLayers images

//var colors_in_marker = 1; //number of color in a marker
var c1_label = "cem";
var c2_label;
var c3_label;


var add_new_place_mode = false; // true when the user is in a mode for adding new place
var markers_and_associated_data = new Array(); // all the markers drawed on the map and the associated data

var id_markers_for = new Array();
id_markers_for['Categories'] = new Array();
id_markers_for['PlaceTypes'] = new Array();
id_markers_for['StatusCeM'] = new Array();

var mode_edit = new Array();

var new_placeMarker;

var last_place_selected = null;

var townId = null;


// marker with color
var color_trad = new Array();
color_trad['0'] = 'w';
color_trad['-1'] = 'd';
color_trad['1'] = 'r';
color_trad['2'] = 'o';
color_trad['3'] = 'g';

var color_trad_text = new Array();
color_trad_text['0'] = 'pas encore pris en compte (blanc)';
color_trad_text['-1'] = 'rejeté (gris)';
color_trad_text['1'] = 'pris en compte (rouge)';
color_trad_text['2'] = 'en cours de résolution (orange)';
color_trad_text['3'] = 'résolu (vert)';

function update_markers_and_associated_data(){
    // removing the information
    $.each(markers_and_associated_data, function(index, marker_and_data) { 
        if (marker_and_data != undefined) { 
            delete marker_and_data[1]; 
        }
    });

    jsonUrlData  =  Routing.generate('wikipedale_place_list_by_city', {_format: 'json', city: townId});
    $.ajax({
        dataType: "json",
        url: jsonUrlData,
        success: function(data) {
            //console.log("update_markers_and_associated_data - done");
            $.each(data.results, function(index, aPlaceData) {
                if (markers_and_associated_data[aPlaceData.id] == undefined) {
                    addMarkerWithClickAction(aPlaceData.geom.coordinates[0],
                        aPlaceData.geom.coordinates[1],
                        displayPlaceDataFunction,
                        aPlaceData);
                }
                else {
                    markers_and_associated_data[aPlaceData.id][1] = aPlaceData;
                }
            });
        },
        complete: function(data) {
            signalement_id = $('#input_place_description_id').val();
            if (signalement_id != "" && signalement_id != undefined) {
                // be sure that a place is selected
                displayRegardingToUserRole();
            }

            $.each(markers_and_associated_data, function(index, marker_and_data) {
                if (marker_and_data != undefined) { 
                    if (marker_and_data[1] == undefined) {
                        marker_and_data[0].erase();
                        marker_and_data = undefined;
                    }
                }
            });
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