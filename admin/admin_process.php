<?php
require_once( 'includes/gob_admin.php' );
require_once( '../lib/reddit.php' );
require_once( '../src/secrets.php' );
require_once( '../src/query.php' );
require_once( 'src/classes/gob.php' );

$reddit = new reddit($reddit_user, $reddit_password);
$gob = new gob($reddit_user, $reddit_password, 'gameofbands');

$mainsubreddit = 'gameofbands';

mod_check();

mysql_connect("localhost", $mysql_user, $mysql_password) or die(mysql_error());
mysql_select_db($mysql_db) or die(mysql_error());

function redirect($pagename){
	header('Location: index.php?view=' . $pagename);
}

// Post the Signup Threads
if(isset($_POST['postroundstart'])){
	$gob->postSignups($_POST['Round']);

	redirect('dashboard');

}

// Post song voting thread
if(isset($_POST['postvote'])){
	$gob->postSongVotingThread();

	redirect('dashboard');

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
	
	
	$result = mysql_query("SELECT * FROM songs WHERE round='$round'") or die(mysql_error());
	
	while($row = mysql_fetch_array($result)){
		$songname = $row['name'];
		
		$postTemplate = "**Team " . $row['teamnumber'] . "** Vote";
		$songTemplate = trim(json_encode($postTemplate), '"');
		
		$i = 0;
		//Run through all comments
		foreach ($commentpool as $parent) {
			//We found the team vote post!
			if (strpos($parent->data->body, $songTemplate) !== false) {
				$votes = $parent->data->ups;
				//Find our post array in the comment pool
				$childpool = $commentpool[$i];
				
				//Run through comments to find out voting comments
				foreach ($childpool->data->replies->data->children as $subchildren) {
					if ($subchildren->data->body == "Music Vote") {
						$musicvote = $subchildren->data->ups;
					}
					
					if ($subchildren->data->body == "Lyrics Vote") {
						$lyricsvote = $subchildren->data->ups;
					}
						
					if ($subchildren->data->body == "Vocals Vote") {
						$vocalsvote = $subchildren->data->ups;
					}
				}
			}
			$i++;
		}
		$sql = "UPDATE songs SET votes = '$votes', musicvote = '$musicvote', lyricsvote = '$lyricsvote',  vocalsvote = '$vocalsvote' WHERE name = '$songname'";
		
		call_db_stay($sql);
	}
	

  // BEST SONGS
  // Calculate table with maximum votes from each round.
  // Join with songs that have the same number of votes.
   $postTemplate = "   
**Round " . $round . " Winners**  

Congratulations to all teams who submitted a song, you're all winners! Except here are the real winners:

**Winning Tracks** \n\n";

$result = mysql_query("
	SELECT  *
	FROM    songs 
	WHERE   round = '$round' 
	HAVING  votes =
	(
	  SELECT  votes
	  FROM    songs 
	  WHERE   round = '$round'
	  ORDER BY votes DESC
	  LIMIT 1  
	)");
	
   while($row = mysql_fetch_array($result)){
	$postTemplate .= "* [" . $row['name'] . "](http://gameofbands.co/song/" . $row['id'] . ") by " . $row['music'] . ", " . $row['lyrics'] . ", " . $row['vocals'];
   }

$postTemplate .= "
  
**Top Players**

* Top Vocalist ";

$result = query_winners('vocals', $round);
	
   while($row = mysql_fetch_array($result)){
	$postTemplate .= "[" . $row['vocals'] . "](http://gameofbands.co/bandit/" . $row['vocals'] . "), ";
   }

$postTemplate .= " \n\n         

* Top Lyricist ";

$result = query_winners('lyrics', $round);
	
   while($row = mysql_fetch_array($result)){
	$postTemplate .= "[" . $row['lyrics'] . "](http://gameofbands.co/bandit/" . $row['lyrics'] . "), ";
   }

$postTemplate .= " \n\n 

* Top Musician ";

$result = query_winners('music', $round);
	
   while($row = mysql_fetch_array($result)){
	$postTemplate .= "[" . $row['music'] . "](http://gameofbands.co/bandit/" . $row['music'] . "), ";
   }
   
$postTemplate .= " \n\n
Congratulations!

Flair is forth coming!";
	$response = $reddit->createStory('Congratulations to the winners of Round ' . $round . "!", '', $mainsubreddit, $postTemplate);

	
	
	mysql_close();
	redirect('dashboard');
	
}

  // Query individual winners
  function query_winners($type, $round) {
    // Calculate table with maximum votes from each round.
    // Join with bandits that have the same number of votes.
    $votetype  = $type.'vote';
    $query = "SELECT  *
	FROM    songs 
	WHERE   round = '$round' 
	HAVING  $votetype =
	(
	  SELECT  $votetype
	  FROM    songs 
	  WHERE   round = '$round'
	  ORDER BY $votetype DESC
	  LIMIT 1  
	)";
	
	return mysql_query($query);
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
	
	// Create list of musician up signed up
	$response = $reddit->getcommentreplies($mainsubreddit,$signupID,$musiciansSignupID);
	$response = $response[1]->data->children[0]->data->replies->data->children;
	$musicianPool = getMusicianList($response,'musician ');
	$playerPool = array();
	
	//Add them to the player pool
	foreach($musicianPool as $bandits) {
		$playerPool[$bandits['name']]['name'] = $playerPool[$bandits['name']]['name'] . $bandits['name'];
		$playerPool[$bandits['name']]['roles'] = $playerPool[$bandits['name']]['roles'] . "musician ";
	}

	
	// Create list of lyricists up signed up
	$response = $reddit->getcommentreplies($mainsubreddit,$signupID,$lyricistsSignupID);
	$response = $response[1]->data->children[0]->data->replies->data->children;
	$lyricistPool = getMusicianList($response,'lyricist');
	
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
	$vocalistPool = getMusicianList($response,'vocalist');
	
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
	$finalbandits['musician'] = array();
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
			
			if(count($finalbandits['musician']) < count($finalbandits['lyricist'])){
				if(count($finalbandits['musician']) < count($finalbandits['vocalist'])) {
					array_push($finalbandits['musician'], $bandits['name']);
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
	shuffle($finalbandits['musician']);
	shuffle($finalbandits['lyricist']);
	shuffle($finalbandits['vocalist']);
	
	/* Print Final Roles
	echo "<h2>Musician</h2>";
	foreach($finalbandits['musician'] as $bandits) {
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
	$shortestRole = min(count($finalbandits['musician']),count($finalbandits['lyricist']),count($finalbandits['vocalist']));
	
	//Set our data ranges for ending the game and when voting ends.
	//It assumes it's running on Sunday start day.
	$startdate = strtotime("+11 day");
	$enddate = strtotime("+14 day");
	$ourtheme = explode(" %$% ", $winningTheme);
	
	// Post our team announcement thread
	$postTemplate = "**Game Of Bands Round " . $round . "**
		
Team assignments are listed in the comments below.

This round will end at **8pm UTC Sunday " . date('M d, Y', $startdate) . "**. Songs must be submitted [here](http://gameofbands.co/user_submitsong) by the end-of-round deadline. Submissions will be linked in a single voting post shortly after.  Voting will continue for 3 days ending at **8pm UTC Wednesday " . date('M d, Y', $enddate) . "**, upon which votes will be tallied, results posted, and flair awarded.

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
		$response = $reddit->addComment($announceID, "**Team " . ($i+1) . "**: \n\n* **Musician:** " . $finalbandits['musician'][$i] . "\n\n* **Lyricist:** " . $finalbandits['lyricist'][$i] . "\n\n* **Vocalist:** " . $finalbandits['vocalist'][$i]);
		
		$teamnumber = $i+1;
		$musician = $finalbandits['musician'][$i];
		$lyricist = $finalbandits['lyricist'][$i];
		$vocalist = $finalbandits['vocalist'][$i];
		
		// Add new team to the database
		$sql = "INSERT INTO teams (round, teamnumber, musician, lyricist, vocalist) VALUES ('$round', '$teamnumber', '$musician', '$lyricist', '$vocalist')";
		call_db_stay($sql);
		
		//Once assigned to a team, remove them from their role lists
		unset($finalbandits['musician'][$i]);
		unset($finalbandits['lyricist'][$i]);
		unset($finalbandits['vocalist'][$i]);
	}
	
	//When we run out of complete teams, create list of players not assigned
	foreach($finalbandits['musician'] as $bandits) {
		$Musicians .= "[" . $bandits . "](http://www.reddit.com/message/compose/?to=" . $bandits . "), " ;
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
**Musician** " . $Musicians . " \n ***
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
	
	call_db($sql,'dashboard');
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

function getMusicianList($searcharray,$role) {
	$musicianPool = array();
	foreach ($searcharray as $children) {
		$authorName = $children->data->author;
		if (!isset($musicianPool[$authorName])) {
			
			$musicianPool[$authorName] = array();
			$musicianPool[$authorName]['name'] = $authorName;
			$musicianPool[$authorName]['roles'] = $role;

		}

    }

	return ($musicianPool);
}

?>