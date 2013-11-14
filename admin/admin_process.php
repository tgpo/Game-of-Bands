<?php
/*************************************

tgpo: The tracking of the post and comments names is flawed. Instead of capturing the name immediately after posting it,
we need to run through the comment json at the end and save off the correct names in the DB for future use.

/**************************************************/


require_once('includes/gob_admin.php');
require_once('../includes/reddit.php');
require_once('../includes/secrets.php');
$reddit = new reddit($reddit_user, $reddit_password);

mod_check();
?>
<?php
mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db("xxxdatabasexxx") or die(mysql_error());

function redirect($pagename){
	header('Location: '.$pagename.'.php');
}

if(isset($_POST['postroundstart'])){
	$postTemplate = "Sometimes folks can't keep their commitment, and their team will pop in here to look for replacements. You may be snatched up quickly!
	
Form up in the comments below and give us a heads up on any team changes.

It's like leave a penny take a penny but for teammates. If you need a teammate to fill in, ask for one here. If you'd like to offer your services or sign up late you can also post here. Play on!

---

**Note:** The comments of established teams are removed, so it's more clear who's still available.";
	$response = $reddit->createStory('Team Consolidation Thread for Round ' . $_POST['Round'] . '.', '', 'waitingforgobot', $postTemplate);
	
	$response = $reddit->getListing("waitingforgobot", 1);
	$consolidationpostID = $response->data->children[0]->data->name;

	$postTemplate = "Reply to the appropriate comment to sign up for this round of Game of Bands.
	
You may sign up for multiple roles, but you will only be selected for one. Only direct replies to the 'sign up comments' will be considered as signing up. Any direct reply to the 'sign up comments' will be considered a sign up, no matter what the comment says. If you change your mind about a sign up please delete your comment.

Press 1 to be returned to the main menu.";
	$response = $reddit->createStory('Signups for Round ' . $_POST['Round'] . '; all roles.', '', 'waitingforgobot', $postTemplate);
	
	$response = $reddit->getListing("waitingforgobot", 1);
	$signuppostID = $response->data->children[0]->data->name;
	
	$response = $reddit->addComment($signuppostID, 'Musicians Reply Here');
	$musiciansSignuppostID = $reddit->getcomments('waitingforgobot', $limit = 1);
	$musiciansSignuppostID = $musiciansSignuppostID->data->children[0]->data->name;
	
	$response = $reddit->addComment($signuppostID, 'Lyricists Reply Here');
	$lyricistsSignuppostID = $reddit->getcomments('waitingforgobot', $limit = 1);
	$lyricistsSignuppostID = $lyricistsSignuppostID->data->children[0]->data->name;
	
	$response = $reddit->addComment($signuppostID, 'Vocalists Reply Here');
	$vocalistSignuppostID = $reddit->getcomments('waitingforgobot', $limit = 1);
	$vocalistSignuppostID = $vocalistSignuppostID->data->children[0]->data->name;

	$postTemplate = "Post theme ideas here. Up and down votes are considered in selecting a winner. Keep in mind that a theme must apply to **all the disciplines** in a team. Nominations which do not meet this criteria, or that have been done previously, will be removed.";
	$response = $reddit->createStory('Theme voting post for Round ' . $_POST['Round'] . '.', '', 'waitingforgobot', $postTemplate);
	
	$response = $reddit->getListing("waitingforgobot", 1);
	$votepostID = $response->data->children[0]->data->name;
	
	$round = $_POST['Round'];
	
	$sql = "INSERT INTO rounds (number, theme, voteID, signupID, musiciansSignupID, lyricistsSignupID, vocalistSignupID, consolidationID) VALUES ('$round', 'NULL', '$votepostID', '$signuppostID', '$musiciansSignuppostID', '$lyricistsSignuppostID', '$vocalistSignuppostID', '$consolidationpostID')";
	
	call_db($sql);

	redirect('index');
}

if(isset($_POST['getsignups'])){
	$round = $_POST['Round2'];
	$result = mysql_query("SELECT * FROM rounds WHERE number='$round'") or die(mysql_error());
	$postIDs = mysql_fetch_array($result);
	$postID = substr($postIDs['signupID'], 3);
	$commentID = substr($postIDs['musiciansSignupID'], 3);
	echo("PostID ".$postID."\n");
	echo("commentID ".$commentID."\n");
	
	$response = $reddit->getcommentreplies('waitingforgobot',$postID,$commentID);
	//print_r($response);
	//redirect('index');
}

if(isset($_POST['addSong'])){
	$round = mysql_real_escape_string( $_POST["round"] );
	$name = mysql_real_escape_string( $_POST["name"] );
	$url = mysql_real_escape_string( $_POST["url"] );
	$lyrics = mysql_real_escape_string( $_POST["lyrics"] );
	$music = mysql_real_escape_string( $_POST["music"] );
	$vocals = mysql_real_escape_string( $_POST["vocals"] );

	$lyricsheet = mysql_real_escape_string( $_POST["lyricsheet"] );
	$votes = mysql_real_escape_string( $_POST["votes"] );
	$lyricsvote = mysql_real_escape_string( $_POST["lyricsvote"] );
	$musicvote = mysql_real_escape_string( $_POST["musicvote"] );
	$vocalsvote = mysql_real_escape_string( $_POST["vocalsvote"] );

	if(isset($_POST['winner']) && 
	   $_POST['winner'] == 'Yes') 
	{
		$winner = true;
	} else {
		$winner = false;
	}

	$sql = "INSERT INTO songs (name, url, music, lyrics, vocals, lyricsheet, round, votes, winner, rating, musicvote, lyricsvote, vocalsvote) VALUES ('$name', '$url', '$music', '$lyrics', '$vocals', '$lyricsheet', '$round', '$votes', '$winner', NULL, '$musicvote', '$lyricsvote', '$vocalsvote')";
	
	call_db($sql);
}
if(isset($_POST['editSong'])){
	
	$id = mysql_real_escape_string( $_POST["id"] );
	$round = mysql_real_escape_string( $_POST["round"] );
	$name = mysql_real_escape_string( $_POST["name"] );
	$url = mysql_real_escape_string( $_POST["url"] );
	$lyrics = mysql_real_escape_string( $_POST["lyrics"] );
	$music = mysql_real_escape_string( $_POST["music"] );
	$vocals = mysql_real_escape_string( $_POST["vocals"] );

	$lyricsheet = mysql_real_escape_string( $_POST["lyricsheet"] );
	$votes = mysql_real_escape_string( $_POST["votes"] );
	$lyricsvote = mysql_real_escape_string( $_POST["lyricsvote"] );
	$musicvote = mysql_real_escape_string( $_POST["musicvote"] );
	$vocalsvote = mysql_real_escape_string( $_POST["vocalsvote"] );

	if(isset($_POST['winner']) && 
	   $_POST['winner'] == 'Yes') 
	{
		$winner = true;
	} else {
		$winner = false;
	}
	
	if(isset($_POST['delete_song']))
	{
		$sql = "DELETE FROM songs WHERE id = '$id'";
		
	} else {
		$sql = "UPDATE songs SET name = '$name', url = '$url' ,music = '$music', lyrics = '$lyrics', vocals = '$vocals', lyricsheet = '$lyricsheet', round = '$round', votes = '$votes', winner = '$winner', rating = NULL, musicvote = '$musicvote', lyricsvote = '$lyricsvote',  vocalsvote = '$vocalsvote' WHERE id = '$id'";
	}
	
	call_db($sql);

}

function call_db($sql){
	if(is_mod()){
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		} else {
			redirect('songlist');
		}
	}

	mysql_close();
}

?>