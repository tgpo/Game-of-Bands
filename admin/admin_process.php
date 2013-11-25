<?php
require_once('includes/gob_admin.php');
require_once('../lib/reddit.php');
require_once('../src/secrets.php');
$reddit = new reddit($reddit_user, $reddit_password);

$mainsubreddit = 'gameofbands';

mod_check();

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_db) or die(mysql_error());

function redirect($pagename){
	header('Location: '.$pagename.'.php');
}

// We need to post the Signup Threads
if(isset($_POST['postroundstart'])){
	$postTemplate = "Reply to the appropriate comment to sign up for this round of Game of Bands.
	
You may sign up for multiple roles, but you will only be selected for one. Only direct replies to the 'sign up comments' will be considered as signing up. Any direct reply to the 'sign up comments' will be considered a sign up, no matter what the comment says. If you change your mind about a sign up please delete your comment.

Press 1 to be returned to the main menu.";
	$response = $reddit->createStory('Signups for Round ' . $_POST['Round'] . '; all roles.', '', $mainsubreddit, $postTemplate);

	
	$postTemplate = "Post theme ideas here. Up and down votes are considered in selecting a winner. Keep in mind that a theme must apply to **all the disciplines** in a team. Nominations which do not meet this criteria, or that have been done previously, will be removed.";
	$response = $reddit->createStory('Theme voting post for Round ' . $_POST['Round'] . '.', '', $mainsubreddit, $postTemplate);
	
	/* Find our new posts and save their IDs for future use */
	sleep(3);
	$getredditlisting = $reddit->getListing($mainsubreddit,5);
	$getredditlisting = $getredditlisting->data->children;
	
	$signupID = titleSearch($getredditlisting,'Signups for Round ' . $_POST['Round'] . '; all roles.');
	$themeID = titleSearch($getredditlisting,'Theme voting post for Round ' . $_POST['Round'] . '.');
	
	/* Post our Signup Comments */
	$response = $reddit->addComment($signupID, 'Musicians Reply Here');
	$response = $reddit->addComment($signupID, 'Lyricists Reply Here');
	$response = $reddit->addComment($signupID, 'Vocalists Reply Here');
	
	/* Find our new comments and save their IDs for future use */
	sleep(3);
	$commentpool = $reddit->getcomments($mainsubreddit,$signupID,5);
	$commentpool = $commentpool->data->children;
	
	$musiciansSignuppostID = commentSearch($commentpool,'Musicians Reply Here');
	$lyricistsSignuppostID = commentSearch($commentpool,'Lyricists Reply Here');
	$vocalistSignuppostID = commentSearch($commentpool,'Vocalists Reply Here');
	
	$round = $_POST['Round'];

	// Save our data to the databse
	$sql = "INSERT INTO rounds (number, theme, signupID, musiciansSignupID, lyricistsSignupID, vocalistSignupID, consolidationID, themeID) VALUES ('$round', 'NULL', '$signupID', '$musiciansSignuppostID', '$lyricistsSignuppostID', '$vocalistSignuppostID', '','$themeID')";
	
	call_db($sql,'songlist');

	redirect('index');
}

// Post song voting thread
if(isset($_POST['postvote'])){
	$result = mysql_query("SELECT * FROM rounds order by number desc limit 1 ") or die(mysql_error()); 
	$round = mysql_fetch_array($result);
	$currentround = $round['number'];
	
	$postTemplate = "All submitted songs can be found on the Game of Bands Website:
[Game of Bands Song Depository, Round " . $currentround . ": " . $round['theme'] . "](http://gameofbands.co/round/" . $currentround . ")

Listen * Vote * Comment";
	$response = $reddit->createStory('Official voting post for Round ' . $currentround, '', $mainsubreddit, $postTemplate);
	
	/* Find our new post and save their IDs for future use */
	sleep(3);
	$getredditlisting = $reddit->getListing($mainsubreddit,5);
	$getredditlisting = $getredditlisting->data->children;
	
	$songvotingthread = titleSearch($getredditlisting,'Official voting post for Round ' . $currentround);
	
	$result = mysql_query("SELECT * FROM songs WHERE round='$currentround'") or die(mysql_error());
	
	while($row = mysql_fetch_array($result)){

		/* Post our song Comments */
		$postTemplate = "**Team " . $row['teamnumber'] . "** Vote\n
* **Music:** " . $row['music'] . "\n
* **Lyrics:** " . $row['lyrics'] . "\n
* **Vocals:** " . $row['vocals'] . "\n
* **Track:** [" . $row['name'] . "](http://gameofbands.co/song/".$row['id'].")";

		$response = $reddit->addComment($songvotingthread, $postTemplate);
	}
	
	sleep(3);
	$commentpool = $reddit->getcomments($mainsubreddit,$songvotingthread,999);
	$commentpool = $commentpool->data->children;
	
	$result = mysql_query("SELECT * FROM songs WHERE round='$currentround'") or die(mysql_error());
	while($row = mysql_fetch_array($result)){
	
		$postTemplate = "**Team " . $row['teamnumber'] . "** Vote";
		$postTemplate = trim(json_encode($postTemplate), '"');
	
		$teamvotecomment = commentContainsSearch($commentpool,$postTemplate);
	
		/* Post our vote Comments */
		$response = $reddit->addComment($teamvotecomment, 'Music Vote');
		$response = $reddit->addComment($teamvotecomment, 'Lyrics Vote');
		$response = $reddit->addComment($teamvotecomment, 'Vocals Vote');
	}
	
	//Save our voting thread for later use
	$sql = "UPDATE rounds SET songvotingthreadID = '$songvotingthread' WHERE number = '$currentround'";
	call_db($sql,index);

}

// Assign Teams, Assign Theme, and Post round starter threads
if(isset($_POST['getsignups'])){
	$round = $_POST['Round2'];
	
	//Get our saved data about this round
	$result = mysql_query("SELECT * FROM rounds WHERE number='$round'") or die(mysql_error());
	$postIDs = mysql_fetch_array($result);
	$signupID = substr($postIDs['signupID'], 3);
	$musiciansSignupID = substr($postIDs['musiciansSignupID'], 3);
	$lyricistsSignupID = substr($postIDs['lyricistsSignupID'], 3);
	$vocalistSignupID = substr($postIDs['vocalistSignupID'], 3);
	$themeID = substr($postIDs['themeID'], 3);
	
	//Make it happen!
	assignTeams($mainsubreddit,$round,$signupID,$musiciansSignupID,$lyricistsSignupID,$vocalistSignupID,getWinningTheme($mainsubreddit,$themeID,$reddit),$reddit);
}

// Determine highest voted song and post winner
if(isset($_POST['postwinners'])){
	$round = $_POST['Round4'];
	
	//Get our saved data about this round
	$result = mysql_query("SELECT * FROM rounds WHERE number='$round'") or die(mysql_error());
	$postIDs = mysql_fetch_array($result);
	$songvotingthread = substr($postIDs['songvotingthreadID'], 3);
	
	$commentpool = $reddit->getpostcomments($mainsubreddit,$songvotingthread,999);
	$commentpool = $commentpool[1]->data->children;
	
	/*echo "<pre>";
	print_r($commentpool);
	echo "</pre>"; */
	
	$result = mysql_query("SELECT * FROM songs WHERE round='$round'") or die(mysql_error());
	
	echo "<h2>Votes</h2>";
	while($row = mysql_fetch_array($result)){
		echo "<br />" . $row['name'] . " - ";
		
		$postTemplate = "**Team " . $row['teamnumber'] . "**";
		//$postTemplate = "**Team " . $row['teamnumber'] . "** Vote";
		$songTemplate = trim(json_encode($postTemplate), '"');
		
		$i = 0;
		//Run through all comments
		foreach ($commentpool as $parent) {
			//We found the team vote post!
			if (strpos($parent->data->body, $songTemplate) !== false) {
				echo "Song Votes: " . $parent->data->ups . "<br />";
				//Find our post array in the comment pool
				$childpool = $commentpool[$i];
				
				//Run through comments to find out voting comments
				foreach ($childpool->data->replies->data->children as $subchildren) {
					if ($subchildren->data->body == "**Music Vote**")
						echo " Music Vote: " . $subchildren->data->ups ;
					
					if ($subchildren->data->body == "**Lyrics Vote**")
						echo " Lyrics Vote: " . $subchildren->data->ups ;
						
					if ($subchildren->data->body == "**Vocal Vote**")
						echo " Vocals Vote: " . $subchildren->data->ups ;
				}
			}
			$i++;
		}
	}
	
	
}

//Runs through Theme Voting post and determines post with highest karma
function getWinningTheme($mainsubreddit,$themeID,$reddit) {
	
	//Get comment data
	$response = $reddit->getpostcomments($mainsubreddit, $themeID,999);
	$postURL = $response[0]->data->children[0]->data->url;
	$response = $response[1]->data->children;
	
	//Determine post with higest karma
	$highVote = 0;
	foreach($response as $theme) {
		$score = $theme->data->ups - $theme->data->downs;
		if($score>$highVote) {
			$winningTheme = $theme->data->body;
			$winningThemeLink = substr($theme->data->name, 3);
			$highVote = $score;
		}
	}
	
	//Send it back
	return $winningTheme . " %$% " . $postURL . $winningThemeLink;
	
}

//Assign Teams, Assign Theme, Post Team Announcement Thread, Post Team Consolidation Thread
function assignTeams($mainsubreddit,$round,$signupID,$musiciansSignupID,$lyricistsSignupID,$vocalistSignupID,$winningTheme,$reddit) {
	
	// Create list of muscians up signed up
	$response = $reddit->getcommentreplies($mainsubreddit,$signupID,$musiciansSignupID);
	$response = $response[1]->data->children[0]->data->replies->data->children;
	$muscianPool = getMuscianList($response,'muscian ');
	$playerPool = array();
	
	//Add them to the player pool
	foreach($muscianPool as $bandits) {
		$playerPool[$bandits['name']]['name'] = $playerPool[$bandits['name']]['name'] . $bandits['name'];
		$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "muscian ";
	}

	
	// Create list of lyricists up signed up
	$response = $reddit->getcommentreplies($mainsubreddit,$signupID,$lyricistsSignupID);
	$response = $response[1]->data->children[0]->data->replies->data->children;
	$lyricistPool = getMuscianList($response,'lyricist');
	
	//Add them to the player pool
	foreach($lyricistPool as $bandits) {
		if(isset($playerPool[$bandits['name']])) {
			//If they're already there, then add their new role to their name
			$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "lyricist ";
		} else {
			//Otherwise make a new entry for them
			$playerPool[$bandits['name']]['name'] = $playerPool[$bandits['name']]['name'] . $bandits['name'];
			$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "lyricist ";
		}
	}


	// Create list of vocalists up signed up
	$response = $reddit->getcommentreplies($mainsubreddit,$signupID,$vocalistSignupID);
	$response = $response[1]->data->children[0]->data->replies->data->children;
	$vocalistPool = getMuscianList($response,'vocalist');
	
	//Add them to the player pool
	foreach($vocalistPool as $bandits) {
		if(isset($playerPool[$bandits['name']])) {
			//If they're already there, then add their new role to their name
			$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "vocalist ";
		} else {
			//Otherwise make a new entry for them
			$playerPool[$bandits['name']]['name'] = $playerPool[$bandits['name']]['name'] . $bandits['name'];
			$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "vocalist ";
		}
	}
	
	//Run throuh our player lists and assign final role. If more than 1 role is signed up for, determine which roll will make the most number of complete teams
	$finalbandits = array();
	$finalbandits['muscian'] = array();
	$finalbandits['lyricist'] = array();
	$finalbandits['vocalist'] = array();

	//Start with people who signed up for 1 role
	foreach($playerPool as $bandits) {
		if(substr_count($bandits['roles'], ' ') == 1) {
			//Assign them to the final role list
			array_push($finalbandits[substr($bandits['roles'], 0, -1)], $bandits['name']);
			
			//Take them out of the player pool since they are now assigned a role
			unset($playerPool[$bandits['name']]);
		}
	}
	
	//Run through people who signed up for 2 roles
	foreach($playerPool as $bandits) {
		if(substr_count($bandits['roles'], ' ') == 2) {
			$roles = explode(" ", $bandits['roles']);
			//See which of their two roles has less people and assign them to it
			if(count($finalbandits[$roles[0]]) < count($finalbandits[$roles[1]])) {
				array_push($finalbandits[$roles[0]], $bandits['name']);
			} else {
				array_push($finalbandits[$roles[1]], $bandits['name']);
			}
			
			//Take them out of the player pool since they are now assigned a role
			unset($playerPool[$bandits['name']]);
		}
	}

	//Run through people who signed up for 3 roles
	foreach($playerPool as $bandits) {
		if(substr_count($bandits['roles'], ' ') == 3) {
			//See which role is most in need and assign player to that role.
			
			if(count($finalbandits['muscian']) < count($finalbandits['lyricist'])){
				if(count($finalbandits['muscian']) < count($finalbandits['vocalist'])) {
					array_push($finalbandits['muscian'], $bandits['name']);
					unset($playerPool[$bandits['name']]);
				} else {
					array_push($finalbandits['vocalist'], $bandits['name']);
					unset($playerPool[$bandits['name']]);
				}
			} else{
				if(count($finalbandits['lyricist']) > count($finalbandits['vocalist'])) {
					array_push($finalbandits['vocalist'], $bandits['name']);
					unset($playerPool[$bandits['name']]);
				} else {
					array_push($finalbandits['lyricist'], $bandits['name']);
					unset($playerPool[$bandits['name']]);
				}
			}
		}
	}
	

	
	//Shake It Up (The Cars)
	shuffle($finalbandits['muscian']);
	shuffle($finalbandits['lyricist']);
	shuffle($finalbandits['vocalist']);
	
	/* Print Final Roles
	echo "<h2>Muscians</h2>";
	foreach($finalbandits['muscian'] as $bandits) {
		print_r($bandits . " ");
	}
	echo "<h2>Lyricists</h2>";
	foreach($finalbandits['lyricist'] as $bandits) {
		print_r($bandits . " ");
	}
	echo "<h2>Vocalists</h2>";
	foreach($finalbandits['vocalist'] as $bandits) {
		print_r($bandits . " ");
	}
	
	echo "<h2>Left Overs</h2>";
	foreach($playerPool as $bandits) {
		print_r($bandits);
	} */
	
	//Determine which role has the least assigned players
	$shortestRole = min(count($finalbandits['muscian']),count($finalbandits['lyricist']),count($finalbandits['vocalist']));
	
	//Set our data ranges for ending the game and when voting ends.
	//It assumes it's running on Sunday start day.
	$startdate = strtotime("+11 day");
	$enddate = strtotime("+14 day");
	$ourtheme = explode(" %$% ", $winningTheme);
	
	// Post our team announcement thread
	$postTemplate = "**Game Of Bands Round " . $round . "**
		
Team assignments are listed in the comments below.

This round will end at **8pm UTC Sunday " . date('M d, Y', $startdate) . "**. Songs must be submitted [here](http://gameofbands.co/user_submitsong.php) by the end-of-round deadline. Submissions will be linked in a single voting post shortly after.  Voting will continue for 3 days ending at **8pm UTC Wednesday " . date('M d, Y', $enddate) . "**, upon which votes will be tallied, results posted, and flair awarded.

By popular upvote count this round's theme is **[" . $ourtheme[0] . "](". $ourtheme[1] .")** \n\n Each team is responsible for contacting its members, and submitting a link to any audio site **that plays their submission in a web browser** There is no distinct \"team consolidation period\".  Team members are **strongly encouraged** to comment in their team thread below *as soon as possible*.  If any team has difficulty assembling, there will be a new team consolidation post shortly after, and teams missing members as well as any registrants who could not be placed or registered late are free to self-organize into new teams.  Non-team submissions are welcome and encouraged, but are not eligible for the official contest win , though maybe for other prizes or w/e;.
--- 
Teams are assigned at random by what Juniper Ale left intact.

Voting rules will be announced in the voting thread.\n\nMeet your teammates and get going!  Good luck to everyone and thanks for participating.

Feel free to join [the chat room](http://kiwiirc.com/client/irc.snoonet.org/gameofbands)";
	$response = $reddit->createStory('Round ' . $round . ' has begun! Teams and rules inside!', '', $mainsubreddit, $postTemplate);
	
	/* Find our new posts and save their IDs for future use */
	sleep(2);
	$getredditlisting = $reddit->getListing($mainsubreddit,5);
	$getredditlisting = $getredditlisting->data->children;

	$announceID = titleSearch($getredditlisting,'Round ' . $round . ' has begun! Teams and rules inside!');
	
	//Run through our final role pools and assign teams
	for ($i = 0; $i < $shortestRole; $i++) {
		$response = $reddit->addComment($announceID, "**Team " . ($i+1) . "**: \n\n* **Muscian:** " . $finalbandits['muscian'][$i] . "\n\n* **Lyricist:** " . $finalbandits['lyricist'][$i] . "\n\n* **Vocalist:** " . $finalbandits['vocalist'][$i]);
		
		//Once assigned to a team, remove them from their role lists
		unset($finalbandits['muscian'][$i]);
		unset($finalbandits['lyricist'][$i]);
		unset($finalbandits['vocalist'][$i]);
	}
	
	//When we run out of complete teams, create list of players not assigned
	foreach($finalbandits['muscian'] as $bandits) {
		$Muscians .= "[" . $bandits . "](http://www.reddit.com/message/compose/?to=" . $bandits . "), " ;
	}

	foreach($finalbandits['lyricist'] as $bandits) {
		$Lyricists .= "[" . $bandits . "](http://www.reddit.com/message/compose/?to=" . $bandits . "), " ;
	}

	foreach($finalbandits['vocalist'] as $bandits) {
		$Vocalists .= "[" . $bandits . "](http://www.reddit.com/message/compose/?to=" . $bandits . "), " ;
	}
	
	//Post team consolidation thread and include our leftover players
$postTemplate = "Sometimes folks can't keep their commitment, and their team will pop in here to look for replacements. You may be snatched up quickly!
Form up in the comments below and give us a heads up on any team changes.

It's like leave a penny take a penny but for teammates. If you need a teammate to fill in, ask for one here. If you'd like to offer your services or sign up late you can also post here. Play on!

**Note** The comments of established teams are removed, so it's more clear who's still available.
***
**Muscians** " . $Muscians . " \n ***
**Lyricists** " . $Lyricists . " \n ***
**Vocalists** " . $Vocalists;
	$response = $reddit->createStory('Team Consolidation Thread for Round ' . $round, '', $mainsubreddit, $postTemplate);
	
	/* Find our new posts and save their IDs for future use */
	sleep(2);
	$getredditlisting = $reddit->getListing($mainsubreddit,5);
	$getredditlisting = $getredditlisting->data->children;

	$consolidationID = titleSearch($getredditlisting,'Team Consolidation Thread for Round ' . $round);
	
	$ourtheme[0] = mysql_real_escape_string($ourtheme[0]);
	
	$sql = "UPDATE rounds SET theme = '$ourtheme[0]', consolidationID = '$consolidationID' WHERE number = '$round'";
	
	call_db($sql,'index');
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
	
	call_db($sql,'songlist');
}
if(isset($_POST['editSong'])){
	
	$id = mysql_real_escape_string( $_POST["id"] );
	$round = mysql_real_escape_string( $_POST["round"] );
	$teamnumber = mysql_real_escape_string( $_POST["teamnumber"] );
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
	if(isset($_POST['approved']) && 
	   $_POST['approved'] == 'Yes') 
	{
		$approved = true;
	} else {
		$approved = false;
	}
	
	if(isset($_POST['delete_song']))
	{
		$sql = "DELETE FROM songs WHERE id = '$id'";
		
	} else {
		$sql = "UPDATE songs SET name = '$name', url = '$url' ,music = '$music', lyrics = '$lyrics', vocals = '$vocals', lyricsheet = '$lyricsheet', round = '$round', votes = '$votes', winner = '$winner', rating = NULL, musicvote = '$musicvote', lyricsvote = '$lyricsvote',  vocalsvote = '$vocalsvote', teamnumber = '$teamnumber', approved = '$approved' WHERE id = '$id'";
	}
	
	call_db($sql,'songlist');

}
if(isset($_POST['editRound'])){
	
	$id = mysql_real_escape_string( $_POST["id"] );
	$theme = mysql_real_escape_string( $_POST["theme"] );
	$signupID = mysql_real_escape_string( $_POST["signupID"] );
	$musiciansSignupID = mysql_real_escape_string( $_POST["musiciansSignupID"] );
	$lyricistsSignupID = mysql_real_escape_string( $_POST["lyricistsSignupID"] );
	$vocalistSignupID = mysql_real_escape_string( $_POST["vocalistSignupID"] );
	$consolidationID = mysql_real_escape_string( $_POST["consolidationID"] );
	$themeID = mysql_real_escape_string( $_POST["themeID"] );
	$songvotingthreadID = mysql_real_escape_string( $_POST["songvotingthreadID"] );
	
	if(isset($_POST['delete_round']))
	{
		$sql = "DELETE FROM rounds WHERE number = '$id'";
		
	} else {
		$sql = "UPDATE rounds SET theme = '$theme', signupID = '$signupID', musiciansSignupID = '$musiciansSignupID', lyricistsSignupID = '$lyricistsSignupID', vocalistSignupID = '$vocalistSignupID', consolidationID = '$consolidationID', themeID = '$themeID', songvotingthreadID = '$songvotingthreadID' WHERE number = '$id'";
	}
	
	call_db($sql,'roundlist');

}

function call_db($sql,$nextPage){
	if(is_mod()){
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		} else {
			redirect($nextPage);
		}
	}

	mysql_close();
}

function call_db_stay($sql){
	if(is_mod()){
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
	}

	mysql_close();
}

function titleSearch($searcharray,$strSearchFor) {
	foreach ($searcharray as $children) {
		if($children->data->title == $strSearchFor)
			return $children->data->name;
    }
}

function commentSearch($searcharray,$strSearchFor) {
	foreach ($searcharray as $children) {
		if($children->data->body == $strSearchFor)
			return $children->data->name;
    }
}

function commentContainsSearch($searcharray,$strSearchFor) {
	foreach ($searcharray as $children) {
		if (strpos($children->data->body, $strSearchFor) !== false)
			return $children->data->name;
    }
}

function getMuscianList($searcharray,$role) {
	$muscianPool = array();
	foreach ($searcharray as $children) {
		$authorName = $children->data->author;
		if (!isset($muscianPool[$authorName])) {
			
			$muscianPool[$authorName] = array();
			$muscianPool[$authorName]['name'] = $authorName;
			$muscianPool[$authorName]['roles'] = $role;

		}

    }

	return ($muscianPool);
}

?>