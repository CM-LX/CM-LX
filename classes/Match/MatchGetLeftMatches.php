<?
class MatchGetLeftMatches extends MyMatch {
    public $body;

    function __construct($body) {
		$this->body = $body;
    }

    function getLeftMatches() {
        $page = isset($_GET['lpage']) ? intval($_GET['lpage']) : 1;
		$page = $page < 1 ? 1 : $page; 
        
        $sql = 'select date from matches group by date';
        $res = $this->body->db->query($sql);
        $num = $this->body->db->num_rows();
        
        $datesPerPage = 10;
        $firstPage = $page > 1 ? '<a href="index.php">First</a> | ' : '';
		$previousPage = $page > 2 ? '<a href="index.php?lpage=' . ($page - 1) . '">Previous</a>' : '';
		$nextPage = $num > ($page * $datesPerPage) ? '<a href="index.php?lpage=' . ($page + 1) . '">Next</a>' : '';
        $lastPage = floor($num / $datesPerPage) + 1;
        $nextPage = $page == ($lastPage - 1) ? '' : $nextPage;
        $lastPage = $page < $lastPage ? ' | <a href="index.php?lpage=' . $lastPage . '">Last</a>' : '';
		$paginationSeparator = $previousPage && $nextPage ? ' | ' : '';

        $matchesList = '';

        $start = $datesPerPage * ($page - 1);

        $sql = 'select date from matches group by date order by date desc limit ' . $start . ', ' . $datesPerPage;
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
            $date = $row['date'];
            $matchesList .= '<div class="main-left-date">' . $date . '</div>';

            $sql2 = 'select id from matches where date="' . $date . '"';
            $res2 = $this->body->db->query($sql2);
            while($row2 = $res2->fetch_assoc())
            {
                $id = $row2['id'];

                $date = $this->get($id, 'date', 'getLeftMatches');
                $team1 = $this->get($id, 'team1', 'getLeftMatches');
                $team2 = $this->get($id, 'team2', 'getLeftMatches');
                $odd1 = $this->get($id, 'odd1', 'getLeftMatches');
                $odd2 = $this->get($id, 'odd2', 'getLeftMatches');
                $winner = $this->get($id, 'winner', 'getLeftMatches');

                $procon = (($odd1 < $odd2) && $winner == 2) || (($odd1 > $odd2) && $winner == 1) ? 'con' : '';
                $procon = (($odd1 > $odd2) && $winner == 2) || (($odd1 < $odd2) && $winner == 1) ? 'pro' : $procon;

                $team1name = $this->body->team->getField($team1, 'name');
                $team2name = $this->body->team->getField($team2, 'name');

                $winner1 = $winner == 1 ? ' class="winner"' : '';
                $winner2 = $winner == 2 ? ' class="winner"' : '';

                $matchesList .= '
                    <div class="main-left-match f-sb ' . $procon . '">
                        <span>
                            <a href="index.php?match=' . $id . '&winner=1"' . $winner1 . '>' . $team1name . '</a> -
                            <a href="index.php?match=' . $id . '&winner=2"' . $winner2 . '>' . $team2name . '</a>
                        </span>
                        <span><a href="index.php?match=' . $id . '&lpage=' . $page . '">Show bet</a> <a href="index.php?match=' . $id . '&draw=1">D</a> ' . $odd1 . '-' . $odd2 . '</span>
                    </div>';
            }
        }

        $html = $matchesList . '
        <div class="main-left-pagination">
            <div>Page ' . $page . '</div>
            <div>' . $firstPage . $previousPage . $paginationSeparator . $nextPage . $lastPage . '</div>
        </div>';
        return $html;
    }
}
?>