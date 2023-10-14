<?
	set_include_path('inc:classes');

	include('errorClass.nba.php');

	include('dbClass.nba.php');
	$db = new dbClass;

	include('pageClass.nba.php');
	$pg	= new pageClass;
	$pg->page = 'matches';

	include('htmlClass.nba.php');
	$html = new htmlClass;

	include('bodyClass.nba.php');
	$body = new bodyClass;

	$body->create($pg->page);
	$html->render();

	unset($body);
	unset($db);
	unset($html);
	unset($pg);
?>