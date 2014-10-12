<div id="google_map" style="height:300px; width:100%;"></div>

<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
// See if we have any coordinates to display
if(typeof coordinates !== 'undefined'){
	// punch them into the map
	var coord = new google.maps.LatLng(coordinates.lat,coordinates.lng);
	var opts = {
    	zoom: 8,
    	center: coord,
    	mapTypeId: google.maps.MapTypeId.ROADMAP
  	};
  	var map = new google.maps.Map(document.getElementById("google_map"), opts);
}
</script>