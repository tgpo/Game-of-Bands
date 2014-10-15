<?php
session_start();
if (!isset($_SESSION['GOB']))
{
	$_SESSION['GOB'] = array();
	$_SESSION['GOB']['loggedin']=false;
}

function is_loggedin(){
	return ($_SESSION['GOB']['loggedin'] ? true : false);
}

function loggedin_check(){
	if (!$_SESSION['GOB']['loggedin'])
	{
		header('Location: index.php');
		die;
	}
}

function is_mod(){
	return ($_SESSION['GOB']['ismod'] ? true : false);
}

function get_username(){
	return $_SESSION['GOB']['name'];
}

function write_username(){
	echo get_username();
}

function get_karma(){
	return $_SESSION['GOB']['karma'];
}

function write_karma(){
	echo get_karma();
}

?>