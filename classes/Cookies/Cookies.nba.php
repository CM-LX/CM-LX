<?php
class Cookies 
{
    public $cm;
    public $loggedOut; // shows content as user
    // public $loggedIn; // shows content as user
    
    // if neither of these is set, then die

    function __construct()
	{
		$cm = isset($_GET['cm']) && intval($_GET['cm']) == 1962;
        if (!$cm) {
            $cm = isset($_COOKIE['ckcm']) && ($_COOKIE['ckcm'] == 1962);
		}
        $this->setCm($cm);
        $this->setCookie("ckcm", 1962);

		$logout = isset($_GET['logout']) ? true: null;
		if (!$logout) {
			$logout = isset($_COOKIE['loggedOut']);
		}
        $this->setLoggedOut($logout);
        $this->setCookie("loggedOut");

        if (!($this->cm && $this->loggedOut)) {
            die();
        }
	}

    private function setCookie ($name, $val = 1): void
    {
        setcookie($name, $val, [ 
            'expires' => time() + 3600,
            'path' => '/',
            'samesite' => 'Strict',
        ]);
    }

    public function getCm (): bool
    {
        return $this->cm;
    }

    private function setCm ($val): void
    {
        $this->cm = $val;
    }

    // public function getLoggedIn (): bool
    // {
    //     return $this->loggedIn;
    // }

    // private function setLogedIn ($val): void
    // {
    //     $this->loggedIn = $val;
    // }

    public function getLoggedOut (): bool
    {
        return $this->loggedOut;
    }

    private function setLoggedOut ($val): void
    {
        $this->loggedOut = $val;
    }
}
?>