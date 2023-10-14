<?
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('log_errors', TRUE);
ini_set('html_errors', TRUE);
ini_set('display_errors', 'On');

class errorClass {
	public function __construct() {
		$this->msg = '';
		$this->page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$this->script = '';
		$this->line = 0;
	}

	public function send() {

		$date = gmdate('D\, j M Y G:i:s T');
		$from = 'NBA';
		$reply = 'webmaster@sxhd01.net';
		$headers = "Date: " . $date . "\r\nFrom: " . $from . " <" . $reply . ">\r\nReply-To: <" . $reply . ">\r\n";
		$msg = "
$this->msg

page: $this->page
script: $this->script
line: $this->line";
		mail("c_madeira@yahoo.com", "NBA error", $msg, $headers, '-f webmaster@sxhd01.net');
		die();
	}
}
?>