<?php
        require_once('src/query.php');
        require_once('src/gob_user.php');

        loggedin_check();
	
  //Attempt at validation that failed....
	//$update_scu = (isset($_POST['scu'])) ? $_POST['scu']  :  "";
	//$update_url = (isset($_POST['url'])) ? $_POST['url']  :  "";
	//$update_tools = (isset($_POST['gear'])) ? $_POST['gear']  :  "";
 
	//if(($update_scu == $update_url) && ($update_url == $update_tools)) header('Location: /index.php');
	
		$db = database_connect();
		$query = $db->prepare('UPDATE bandits SET soundcloud_url="'  .  $_POST['scu']  .  '", website="'  .  $_POST['url']  .  '", tools="'  .  $_POST['gear']  .  '" WHERE name="'  .  get_username()  .  '"');
		$query->execute();
		header('Location: /bandit/'  .  get_username());

?>

		
