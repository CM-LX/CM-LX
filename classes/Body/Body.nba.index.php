<?
	$html = '';
	$match = isset($_GET['match']) ? intval($_GET['match']) : 0;		
	if($match) {
		$this->setTitle('BETS FOR MATCH ' . $match);

		$html .= $this->betShow->show($match);
	} else {
		$this->setTitle('NEXT MATCHES');

		$sql = 'select id from matches where final=0 order by date desc, id';
		$res = $this->db->query($sql);
		while($row = $res->fetch_assoc())
		{
			$id = $row['id'];
			$html .= $this->betShow->show($id);
			$this->bet->calculate($id);
		}
	}
?>