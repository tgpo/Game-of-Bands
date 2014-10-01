/**
 * Relies on google & geocomplete libraries.. and javascript.. and ajax.. 
 * Should really be called /lib/js/xmas_gob_geocode.js or something useful.. ;-)
 */

(function($) {
	$(document).ready(function(){
	// return number to two decimal places
	var twoDP = function(lots_of_decimals){ 
		return parseFloat(Math.round(lots_of_decimals * 100) / 100).toFixed(2); 
	}
	var lat = lng = user_city = autodetected = false;
	
	// query server for details of any reasonably close teams.. well, within 500 kms
	var find_teams = function(){
		$.ajax({
			type: "GET",
			url: '/src/xmas.php?type=jsonteams',
			data: {'lat': lat, 'lng': lng },
			success: function(response){
				//var r =  eval('(' + response + ')');
				var r = response.teams;
				console.log("Found " + r.length + " teams within 200 kms");
				if(r.length){
					$('#current_teams').html('<thead><tr><th>Team Name</th><th>City</th><th>Distance</th></tr></thead>');
				}
				// Add each team to the table
				$.each(r,function(key,value){
					// create a row for it, with links to view
					$('#current_teams tr:last').after('<tr><td><a href="/xmas/team/'
					+ value.tid + '" title="View team">' + value.team + '</a></td><td><a href="/xmas/city/' 
					+ value.cid + '">' + value.city +'</a></td><td>'
					+ twoDP(value.distance) + ' kms</td></tr>');
				});
				$('#find_city_note, #location_note').hide();
				if(autodetected){
					$('#autodetected_note').html('<h3>Teams found based on browser location:</h3>').show();
				}
				$('#new_team_button,#new_team_name').removeAttr('disabled');
			},
			error: function(){ 
				//received failure notification from server, either there really was a problem, or.. 
				//$('#current_teams').html('');
				$('#autodetected_note').html('<h3>Try creating a team.</h3>').show();
				$('#new_team_button,#new_team_name').removeAttr('disabled');
			},
		});
	}
	var update_c = function(name){
		user_city = (name == false) ? '?' : name;
		$("#new_team_city").val(user_city);
	}

	var callback_position = function(p){
		autodetected = true;
		lat = p.coords.latitude;
		lng = p.coords.longitude;
		find_teams();
		console.log("Browser lat/lng: " + lat + '/' + lng);
	}
	// Ask user for location permission
	navigator.geolocation.getCurrentPosition(callback_position);
	
    $("#city").geocomplete()
      .bind("geocode:result", function(event, result){
          lat = result.geometry.location.k;
          lng = result.geometry.location.B; //Why these variables Googs??
          var nam = result.formatted_address;
        console.log("Result: " + nam);
        update_c(nam);
        /* Send coordinates to server, search for teams */
        find_teams();
      })
      .bind("geocode:error", function(event, status){
    	  console.log("ERROR: " + status);
    	  // Hide table, ensure button disabled.
    	  $('#current_teams').html('');
		  $('#autodetected_note').show();
		  $('#new_team_button,#new_team_name').attr('disabled','disabled');
      });
      
     $('#new_team_button').on('click',function(){
    	 if($('#new_team_city').val() == 'Somewhere'){
				alert("Please, find your city first.");
				return;
			}
    	 /* Forward user to the url constructed from their selections*/
		location.href= '/xmas/create_team'
		+ '?lng=' + lng 
		+ '&lat=' + lat 
		+ '&team_name=' + $('#new_team_name').val() 
		+ '&city_name=' + $('#new_team_city').val();
	 });
	});
})(jQuery);