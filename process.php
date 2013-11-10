<?php

if ($song=$_GET['song']) {
	header("Location: viewSong.php?song=".$song); // redirect to viewSong
	exit();
} elseif ($bandit=$_GET['bandit']) {
	header("Location: viewBandit.php?bandit=".$bandit); // redirect to viewBandit
	exit();
} elseif ($round=$_GET['round']) {
	header("Location: viewRound.php?round=".$round); // redirect to viewRound
	exit();
} else {
	header("Location: index.php"); // revert to index
	exit();
}

?>