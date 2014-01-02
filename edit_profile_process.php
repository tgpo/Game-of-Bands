<?php
        require_once('src/query.php');
        require_once('src/gob_user.php');

        loggedin_check();
	
		$db = database_connect();
		$query = $db->prepare('UPDATE bandits SET soundcloud_url=:scu, website=:url, tools=:gear WHERE name="'  .  get_username()  .  '"');
		$query->execute(array('scu' => $_POST['scu'],'url' => $_POST['url'],'gear' => $_POST['gear']);
		header('Location: /bandit/'  .  get_username());

?>

		
