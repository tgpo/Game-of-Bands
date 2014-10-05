<?php
/**
 * Functions for team-members and team-owners to manipulate their team.
 * 
 * This should include:
 * 
 * Adding their song (Owner)
 * Naming their team (Owner)
 * Inviting other bandits, or non-bandits (Owner)
 * Waiting for confirmation (Any bandit, approved by Owner)
 * 
 */
if(!$city_id) die(get_issue_link('XM:FT:XCP:City_Id_Fail'));
?>
<h3>Control Panel</h3>
<?php
// Test for team creator
$creator = get_one('SELECT creator FROM xmas_teams WHERE team_id = ' . $city_id);
if(bandit_id() == $creator['creator']){
	?>
	<div id="teamacp">
	{{ TEAM CREATOR FUNCTIONS }}
	</div>
	<?php 
	// TODO: disable team-submission until all team-members have agreed & nominated & song submitted to SC
	// TODO: show list of pending bandits, with checkbox approval and button to submit to server
	// TODO: royalty split interface, table of approved bandits with input fields indicating current royalty split, allowing change (postback/json)
	// TODO: rename team input box
}
?>
<div id="teamcp">
	{{TEAM CONTROL PANEL}}
	<?php 
	// TODO: display membership status (alter table bandit, add xmas_team_status=bool(approved|pending)
	// TODO: display charity inputs (display text if already nominated) (autocomplete on charity list?), or select list of currently nominated charities for simple "click" nomination.
	?>
	<label for="nominate_name">Charity Name:</label>
	<input type="text" id="nominate_name" title="The name of the registered charity you wish to nominate" />
	<label for="nominate_locality">Charity Location:</label>
	<input type="text" id="nominate_locality" title="Charities are not all global"></input>
	<label for="nominate_email">Charity email address:</label>
	<input type="text" id="nominate_email" title="We will need to contact this charity, please use the best address for that."></input>
	
	<?php // Country/region of charity could be inferred from locality of team's city? 
	// TODO: Create db schema for charities.. will require table (id=auto/int(11),name=text,locality=text,gov_id=text,email=text,contacted=bool,message=text,number_of_nominiations)
	// TODO: roles?
	// TODO: display submit song field, unless song already submitted, then display SC link/widget 
	// TODO: disable all inputs unless T&C agreed to, otherwise show link, create admin interface.

	 ?>
</div>