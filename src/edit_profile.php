<!--<script>
    $(document).ready(function(){
        $("#banditProfile").validate({
            rules: {
                web: {
                    url: true
                },
                scu: {
                    url: true
                }
            }
        });
    });
</script>-->

<?php

    require_once("query.php");
    $db = database_connect();
    $query = $db->prepare('SELECT soundcloud_url, tools, website FROM bandits WHERE name=\'' . get_username() .  '\'');
    $query->execute();
    $scname = "Soundcloud";
    $webname = "Website";
    $tname = "Tools/Gear";
	
	while($row = $query->fetch(PDO::FETCH_ASSOC)){
		$scu = $row['soundcloud_url'];
		$url = $row['website'];
		$gear = $row['tools'];
	       
		echo 	
		'<form id="banditProfile" action="/edit_profile_process" method="post">
			<table class="edit_profile">
				<th colspan="2">Edit Profile</th>
				<tr>
					<td><label>'  .  $webname  .  '</label></td>
					<td><input type="text" id="url" name="url" type="url" value="'  .  $url  .  '" /></td>
				</tr>
				<tr>
					<td><label>'  .  $scname  .  '</label></td>
					<td><input type="text" id="scu" name="scu" type="url" value="'  .  $scu  .  '" /></td>
				</tr>
				<tr>
					<td><label>'  .  $tname  .  '</label></td>
					<td><textarea rows="4" cols="25" id="gear" name="gear">'  .  $gear  .  '</textarea></td>
				</tr>
				<tr>
					<td colspan="2" class="center_submit"><input  type="submit" value="Update Profile" /></td>
				</tr>
			</table>
		</form>';
	
	}

?>		
