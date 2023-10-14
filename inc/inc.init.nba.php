<?php
	set_include_path('classes:../inc:../classes');

	ini_set('log_errors', TRUE);
	ini_set('html_errors', TRUE);
	ini_set('display_errors', 'On');
	ini_set('error_reporting', E_ALL);
	error_reporting(E_ALL);

	include('Error/Error.nba.php');
	
	include('Page/Page.nba.php');
	$pg	= new Page;

	include('Body/Body.nba.php');
	$body = new Body;
?>