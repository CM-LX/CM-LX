<?
class pageClass {
	public function __construct()
	{
		global $db;

/*******************
* SERVER
********************/
		$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$host	= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

/*******************
* GET
********************/
		$cm = isset($_GET['cm']) ? intval($_GET['cm']) : 0;
		$this->id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/*******************
* COOKIES
********************/
		if(!$cm) {
			$cm = isset($_COOKIE['ckcm']) && ($_COOKIE['ckcm'] == 1962);
		}
		if($cm) {
			// setcookie('ckcm', 1962, time() + 3600, '/; samesite=lax');
			setcookie('ckcm', 1962, [ 
				'expires' => time() + 3600,
				'path' => '/',
				'samesite' => 'Strict',
			]);
		} else {
			die();
		}
	}
}
?>