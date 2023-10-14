<?php
class Cookies 
{
    public $cm;
    public $loggedOut; // shows content as user
    public $loggedIn; // shows content as user
    
    // if neither of these is set, then die

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
}
?>