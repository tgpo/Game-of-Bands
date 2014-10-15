
<div id="team_admin_panel">
  <label for="team_name">Team Name:</label><input type="text"
    value="<?php echo $team->getName();?>" id="team_name" /> <input
    id="save_team_name" type="button" value="Save new team name" />
  <div id="charity_nomination">
		<?php
// Get charity information, if set
$charity = $charities = false;
if ($team->hasCharity ()) {
    $charity = new Charity ( $team->getCharity () );
} else {
    $charities = Charity::getList ();
}
?>
	<div id="teamacp">
      <pre>// TODO: disable team-submission until all team-members have agreed & nominated & song submitted to SC</pre>
      <s>// TODO: show list of pending bandits</s>, with checkbox
      approval <s>and button to submit to server</s> <s>// TODO: rename
        team input box backend</s>
	<?php
echo $team->getTeamApprovalButtons ();
?>
<pre>
//TODO: royalty split interface, table of approved bandits with input fields indicating current royalty split, allowing change (postback/json)
</pre>
      <table id="creator_team_list">
        <thead>
          <tr>
            <td>Status</td>
            <td>Name</td>
            <td>Role</td>
            <td>Share</td>
            <td>Agreed</td>
          </tr>
        </thead>
<?php
$r_team = $team->getListObjs ();
$roles = $team->getBanditRoles ();
$options = '<select class="role">';
foreach ( $roles as $title => $value ) {
    $options .= "<option value=\"$value\">$title</option>";
}
$options .= '</select>';

foreach ( $r_team as $bandit ) {
    // TODO: Create inputs for shares allowing creator to adjust.
    if ($bandit->getRole () == '') {
        $role = $options;
    } else {
        $role = $bandit->getRole ();
    }
    echo '<tr id="' . $bandit->getId () . '"><td>' . $bandit->getStatus () . '</td><td>' . $bandit->getName () . '</td><td>' . $role . '</td><td>' . $bandit->getShare () . '</td><td>' . ($bandit->hasAgreedToTermsAndConditions () ? 'Y' : 'N') . '</td></tr>';
}
?>
</table>
      <input type="button" class="save_team" />
<?php
// If we haven't specified a charity, we should have an array built from all previous charities, allowing the team '
// creator to simply select one.. simpler far than digging through the details to nominate a new one.
// Thus making it more likely that they will actually do it.
if ($charities) {
    echo '<label for="existing_charity">Select existing charity:</label> <select id="existing_charity">';
    foreach ( $charities as $c ) {
        echo '
      <option value="' . $c ['id'] . '">' . $c ['name'] . '</option>';
    }
    echo '
    </select>';
}
if (! $team->hasCharity ()) {
    fragment ( 'charity_input_form' );
    echo '<span id="status">Current charity status is: ' . $charity->getStatus () . '</span><br />';
    ?>
			<input type="button" value="Save Charity"
        title="This form is only shown once!" class="save_charity" />
			<?php
} else {
    echo 'Nominated Charity: <a href="/xmas/charity/' . $charity->getId () . '">' . $charity->getName () . '</a> <span>Contact admins to change</span>';
}
?>
	</div>
  </div>