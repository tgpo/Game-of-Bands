<?php
require_once ($here . '/../classes/class.xteam.php');
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
if(!$team) die(get_issue_link('XM:FT:XCP:Team_Details_Fail'));
?>
<script type="text/javascript">
$(document).ready(function(){
    $('#charity_nomination').on('click','input.save_charity',function(){
    	 var parent = $(this).parent();
    	 if($('#existing_id').length){
    		 data =  {
    	    	  'existing_id': $('#existing_id:selected').val() 
    	    };
    	 }else{
    		 data =  {
				 'name': $('#nominate_name').val(), 
				 'locality': $('#nominate_locality').val(), 
				 'email': $('#nominate_email').val(), 
				 'id': $('#nominate_id').val()
    		};
    	 }
		 $.ajax({
			type: "GET",
			url: '/src/xmas.php?type=jsonsetcharity',
			data: data,
			success: function(r){
				parent.append('<span>Saved.</span>.');
			}
 		 });
     });
});
</script>
<h3>Control Panel</h3> 
<?php 
// Test for team creator
if(bandit_id() == $team->getCreator()){
	// Get location of team
	$city = get_one('SELECT * FROM cities WHERE id = ' . $city_id);
	// Get charity information, if set
	$charity = $charities = false;
	if($team->hasCharity()){
		$charity = new Charity($team->getCharity());
	}else{
		$charities = Charity::getList();
	}
	?>
	<div id="teamacp">
<pre>// TODO: disable team-submission until all team-members have agreed & nominated & song submitted to SC
// TODO: show list of pending bandits, with checkbox approval and button to submit to server</pre>
	<?php
	foreach($team_members as $t){
		$td = get_one('SELECT * FROM bandits WHERE name=:name',array('name' => $t));
		if($td['xmas_team_status'] == 'pending'){
			echo a_bandit($t) . ' is still pending. <input type="button" value="Approve" class="approve_member"/> <br />';
		}
	}
?>
<pre>
//TODO: royalty split interface, table of approved bandits with input fields indicating current royalty split, allowing change (postback/json)
</pre>	
<s>// TODO: rename team input box</s> backend
	<div>
		<label for="team_name">Team Name:</label><input type="text" value="<?php echo $team_details['name'];?>" id="team_name" />
	</div>
	<div id="charity_nomination">
		<?php 
		// If we haven't specified a charity, we should have an array built from all previous charities, allowing the team
		// creator to simply select one.. simpler far than digging through the details to nominate a new one.
		// Thus making it more likely that they will actually do it.
		if($charities){
			echo '<label for="existing_charity">Select existing charity:</label>
 				<select id="existing_charity">';
			foreach($charities as $c){
				echo '<option value="' . $c['id'].'">' . $c['name'] . '</option>';
			}
			echo '</select>';
		}
		if(!isset($charity['name'])){
			?>
			<label for="nominate_name">Charity Name:</label><br />
			<input type="text" id="nominate_name" title="The name of the registered charity you wish to nominate"/><br />
			<label for="nominate_locality">Charity Location (Ideally should operate in the same City as the team itself):</label><br />
			<input type="text" id="nominate_locality" title="Charities are not all global"/><br />
			<label for="nominate_email">Charity email/web address (We will need to contact them to arrange payment):</label><br />
			<input type="text" id="nominate_email" title="We will need to contact this charity, please use the best address for that."/><br />
			<label for="nominate_id">Charity Identifier (Registered charities have specific identifiers):</label><br />
			<input type="text" id="nominate_id" title="Charities are only 'legal' if registered in the government of their area of operation, governments typically issue a unique identifier indicating their status as a registered charity, and allowing donators to look up the status."/> <br />
			
			<?php 
			echo "<span id=\"status\">Charity status is: {$charity['status']}</span><br />";
			?>
			<input type="button" value="Save Charity" class="save_charity"  />
			<?php 
		}else{
			echo 'Selected charity: <a href="/xmas/charity/' . $charity['id'] . '">' . $charity['name'] . '</a>';
		}?>
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