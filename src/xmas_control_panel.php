<?php
require_once ('class.xteam.php');
require_once ('class.bandit.php');
require_once ('class.charity.php');
/**
 * Functions for team-members and team-owners to manipulate their team.
 *
 * This should include:
 *
 * Adding their song (Owner)
 * Naming their team (Owner)
 * Inviting other bandits, or non-bandits (Owner)
 * Waiting for confirmation (Any bandit, approved by Owner)
 */
if (! $city)
    die ( get_issue_link ( 'XM:FT:XCP:City_Fail' ) );
if (! $team)
    die ( get_issue_link ( 'XM:FT:XCP:Team_Fail' ) );
    // Create our bandit.
$bandit = new Bandit ( get_bandit_id () );
?>
<script type="text/javascript" src="/src/js/xmas_cp.js"></script>
<h3>Team Control Panel</h3>
<?php
// Test for team creator
if ($bandit->getId () == $team->getCreator ()) {
    include_once ('xmas_admin_panel.php');
}

?>
<div id="teamcp">
	<?php echo '<span id="bandit_status">' . $bandit->getStatus () . '</span>';?>

	// TODO: display submit song field, unless song already submitted, then display SC link/widget
	<?php if($team->hasUrl()){ //Build widget
?><script type="text/javascript">
     var team_url ="<?php echo $team->getUrl();?>";

	   if(typeof widget !== 'undefined'){
	       widget.load(team_url, { // Tell the widget what URL we want to play
					auto_play: true,
					sharing: true,
					liking: true,
					show_artwork: false,
				});
	   }
	   </script>
	}?>
	<label for="upload_song">Upload your completed song: </label><input
      type="file" id="upload_song" />
	// TODO: disable all inputs unless T&C agreed to, otherwise show link, create admin interface.
	<br />
   <a href="???::TODO::"
      title="Download a PDF of the complete terms and conditions">Download terms and conditions</a>
</div>