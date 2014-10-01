<script src="/src/js/xmas.js"></script>

<div class="ui-widget">
	<h3 id="location_note">Ensure you accept the location permision or type
		your city below</h3>
	<label for="city">Find your city: </label> <input id="city"
		class="input-large" type="text" />
</div>
<div class="ui-widget" style="margin-top: 2em; font-family: Arial"></div>
<div id="autodetected_note"></div>
<div id="existing_teams">
	<table id="current_teams"></table>
</div>
<div id="new_city">
	<h3>No teams near you?</h3>
	<label for="new_team_button">Find your city above, then enter a team name:</label>
	<input id="new_team_city" type="input" disabled="disabled"
		value="Somewhere" /> <input id="new_team_name" type="input"
		disabled="disabled" value="Team Name" /> <input id="new_team_button"
		type="button" title="Create a team" value="Create" disabled="disabled" />
	<span id="find_city_note">Note: First find your city above</span>
</div>
