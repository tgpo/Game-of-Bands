<?php
    require_once('src/query.php');
    require_once('src/gob_user.php');

    loggedin_check();
	        
    $scu = filter_input(INPUT_POST, 'scu', FILTER_SANITIZE_URL);
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $gear = filter_input(INPUT_POST, 'gear', FILTER_SANITIZE_SPECIAL_CHARS);
    $bandit = get_username();

	$db = database_connect();
	$query = $db->prepare('UPDATE bandits SET soundcloud_url=:scu, website=:url, tools=:gear WHERE name=:bandit');
	$query->execute(array('scu' => $scu,'url' => $url,'gear' => $gear,'bandit' => $bandit);
	header('Location: /bandit/'  .  $bandit);

?>

			
