<?
	include('inc/inc.init.nba.php');

	$pg->page = 'index';

	$body->html($pg->page);

	$pg->render($body->html);

	include('inc/inc.clean.nba.php');
?>