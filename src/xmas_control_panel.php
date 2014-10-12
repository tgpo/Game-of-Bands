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
if(!$team_details) die(get_issue_link('XM:FT:XCP:Team_Details_Fail'));// Xmas:Function(show_team):XmasControlPanel:Msg, also, fairly unique, should show up pretty easily in a grep.
?>
<h3>Control Panel</h3>
<?php
// Test for team creator
if(bandit_id() == $team_details['creator']){
	// Get location of team
	$city = get_one('SELECT * FROM cities WHERE id = ' . $city_id);
	// Get charity information, if set
	$charity = false;
	if(strlen($team_details['nominate_charity'])){
		$charity = get_one('SELECT * FROM charities WHERE id=:id',array('id',$team_details['nominate_charity']));
	}else{
		$charities = sql_to_array('SELECT id,name FROM charities');
	}
	?>
	<div id="teamacp">
	{{ TEAM CREATOR FUNCTIONS }}
	</div>
	<?php 
	// TODO: disable team-submission until all team-members have agreed & nominated & song submitted to SC
	// TODO: show list of pending bandits, with checkbox approval and button to submit to server
	// TODO: royalty split interface, table of approved bandits with input fields indicating current royalty split, allowing change (postback/json)
	// TODO: rename team input box
	?>
	<div>
		<label for="team_name">Team Name:</label><input type="text" value="<?php echo $team_details['name'];?>" id="team_name" />
	</div>
	<div id="charity_nomination">
		<?php 
		// If we haven't specified a charity, we should have an array built from all previous charities, allowing the team
		// creator to simply select one.. simpler far than digging through the details to nominate a new one.
		// Thus making it more likely that they will actually do it.
		if(isset($charities)){
			echo '<label for="existing_charity">Select existing charity:</label>
 				<select id="existing_charity">';
			foreach($charities as $c){
				echo '<option value="' . $c['id'].'">' . $c['name'] . '</option>';
			}
			echo '</select>';
		}?>
		<label for="nominate_name">Charity Name:</label>
		<input type="text" id="nominate_name" title="The name of the registered charity you wish to nominate" <?php 
			if(strlen($charity['name'])){
				// We've got one! -- Disable it so it can't be modified, we'd ignore any change anyway.
				echo 'disabled="disabled" val="' . $charity['name'] . '" ';	
			}
		?>/>
		<label for="nominate_locality">Charity Location (Ideally should operate in the same City as the team itself):</label>
		<input type="text" id="nominate_locality" title="Charities are not all global" <?php 
			if(strlen($charity['locality'])){echo 'disabled="disabled" val="' . $charity['locality'] . '" ';}
		?>/>
		<label for="nominate_email">Charity email/web address (We will need to contact them to arrange payment):</label>
		<input type="text" id="nominate_email" title="We will need to contact this charity, please use the best address for that." <?php 
			if(strlen($charity['email'])){echo 'disabled="disabled" val="' . $charity['email'] . '" ';}
		?>/>
		<label for="nominate_id">Charity Identifier (Registered charities have specific identifiers):</label>
		<input type="text" id="nominate_id" title="Charities are only 'legal' if registered in the government of their area of operation, governments typically issue a unique identifier indicating their status as a registered charity, and allowing donators to look up the status." <?php 
			if(strlen($charity['charity_id'])){echo 'disabled="disabled" val="' . $charity['charity_id'] . '" ';}
		?>/> 
		
		<?php 
		echo "<span id=\"status\">Charity status is: {$charity['status']}</span>";
		?>
	</div>
<?php 
}
?>
<div id="teamcp">
	{{TEAM CONTROL PANEL}}
	<?php 
	// TODO: display membership status (alter table bandit, add xmas_team_status=bool(approved|pending)
	
	// TODO: Create db schema for charities.. will require table (id=auto/int(11),name=text,locality=text,gov_id=text,email=text,contacted=bool,message=text,number_of_nominiations)
	// TODO: roles?
	// TODO: display submit song field, unless song already submitted, then display SC link/widget 
	// TODO: disable all inputs unless T&C agreed to, otherwise show link, create admin interface.

	 ?>
</div>