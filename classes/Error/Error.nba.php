<?php
	ini_set('log_errors', TRUE);
	ini_set('html_errors', TRUE);
	ini_set('display_errors', 'On');
	ini_set('error_reporting', E_ALL);
	error_reporting(E_ALL);

	class MyError {
		public $msg;
		public $script;
		public $line;
		private $headers;
		private $reply_to = 'webmaster@sxhd01.net';
		
		function __construct() {
			$page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
			$date = gmdate('D\, j M Y G:i:s T');
			$this->headers = "Date: " . $date . "\r\nFrom: NBA <" . $this->reply_to . ">\r\nReply-To: <" . $this->reply_to . ">\r\n"; // must be double quotes
			$this->msg = "
	$this->msg
	
	page: $page
	script: $this->script
	line: $this->line";
		}

		function send() {
			mail("c_madeira@yahoo.com", "NBA error", $this->msg, $this->headers, '-f ' . $this->reply_to);
			die();
		}
	}
?>