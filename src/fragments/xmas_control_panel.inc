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
if(!$city_id) die(get_issue_link('XM:FT:XCP:City_Id_Fail!'));
?>

{{TEAM CONTROL PANEL}}
<?php 
// Test for team creator
$creator = get_one('SELECT creator FROM xmas_teams WHERE team_id = ' . $city_id);