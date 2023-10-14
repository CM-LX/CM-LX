<?php
class Page {
	public $cm;
	public $logout;

	function __construct()
	{
		$cm = isset($_GET['cm']) && intval($_GET['cm']) == 1962;
		$this->cm = $cm ? $cm : 0;

		if(!$cm) {
			$this->cm = isset($_COOKIE['ckcm']) && ($_COOKIE['ckcm'] == 1962);
		}

		if($this->cm) {
			setcookie('ckcm', 1962, [ 
				'expires' => time() + 3600,
				'path' => '/',
				'samesite' => 'Strict',
			]);
		}

		$logout = isset($_GET['logout']) ? true: null;

		if(!$logout) {
			$logout = isset($_COOKIE['logout']);
		}

		if($logout) {
			setcookie('logout', 1, [ 
				'expires' => time() + 3600,
				'path' => '/',
				'samesite' => 'Strict',
			]);
		}

		if(!$this->cm && !$logout) {
			die();
		}
	}

	function render($html) {
		$html = 
'<!DOCTYPE html>
<html>
	<head>
		<link rel="shortcut icon" href="http://sxhd01.net/nba/favicon.ico">
		<link rel="stylesheet" href="nba.css" type="text/css">
		<meta http-equiv="content-language" content="pt-pt">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>NBA</title>
	</head>
	<body>
' . $html . '
	</body>
</html>';
		echo $html;
	}
}
?>