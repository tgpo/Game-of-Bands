<?php
session_start();

function mod_check(){
	if (!$_SESSION['GOB']['loggedin'])
	{
		header('Location: ../index.php');
		die;
	} else {	
		if (!$_SESSION['GOB']['ismod'])
		{
			header('Location: ../index.php');
			die;
		}
	}
}

function is_mod(){
	return ($_SESSION['GOB']['ismod'] ? true : false);
}
?>