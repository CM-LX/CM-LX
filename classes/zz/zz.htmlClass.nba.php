<?
class htmlClass {
	public function render() {
		global $body, $head, $pg;

		$this->html = 
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
' . $body->html . '
	</body>
</html>';
		echo $this->html;
	}
}
?>