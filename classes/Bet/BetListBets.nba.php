<?php
class BetListBets extends Bet {
    public $body;

    function __construct($body) {
        $this->body = $body;
        // $this->getMonths();

    }

    function colorFormat($value) {
        $color = $value >= 0 ? 'green' : 'red';
        return '<span class="' . $color . '">' . number_format($value, 2) . '</span>'; 
    }

    function getMonths() {
        $html = '';

        $minBalance = 0;
        $maxBalance = 0;

        $sql = 'select date_format(date, "%Y-%m") as month from matches where final=1 group by month order by month asc'; // echo "BetLisBets::getMonths $sql<br>";
		$res = $this->body->db->query($sql);
		while($row = $res->fetch_assoc()) {
			$month = $row['month']; // echo "BetLisBets::getMonths MONTH: $month<br>";

            $sql2 = 'select sum(min) as sumMin from gains where date like "' . $month . '%"';
            $res2 = $this->body->db->query($sql2);
            $row2 = $res2->fetch_assoc();
			$sumMin = $row2['sumMin']; 
            $minBalance += $sumMin;

            $sql2 = 'select sum(max) as sumMax from gains where date like "' . $month . '%"';
            $res2 = $this->body->db->query($sql2);
            $row2 = $res2->fetch_assoc();
			$sumMax = $row2['sumMax']; 
            $maxBalance += $sumMax;

            $html .= 
'       <div class="first-level-title grid" onClick="toggleVisibility(\'' . $month . '\')">
            <span class="left">' . $month . '</span>
            <span class="span5 grid">
                <span class="span2 left">MIN</span>
                <span class="span5 f-sb">
                    <span>Gain</span>
                    <span>' . $this->colorFormat($sumMin, 2) . '</span>
                </span>
                <span class="span5 f-sb">
                    <span>Balance</span>
                    <span>' . $this->colorFormat($minBalance, 2) . '</span>
                </span>
            </span>
            <span></span>
            <span class="span5 grid">
                <span class="span2 left">MAX</span>
                <span class="span5 f-sb">
                    <span>Gain</span>
                    <span>' . $this->colorFormat($sumMax, 2) . '</span>
                </span>
                <span class="span5 f-sb">
                    <span>Balance</span>
                    <span>' . $this->colorFormat($maxBalance, 2) . '</span>
                </span>
            </span>
        </div>
        <div id="' . $month . '" class="hidden" style="display:none">
            <div class="box">
        ' . $this->listMonthBets($month) . '
            </div>
        </div>';
        }
        return $html;
    }

    function listMonthBets($month) {
        $html = '';

        $minBalance = 0;
        $maxBalance = 0;
        
        $sql = 
        
        $sql = 'select date from matches where date_format(date, "%Y-%m")="' . $month . '" && final=1 group by date order by date'; // echo "$sql<br>";
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
            $date = $row['date'];
            
            $dailyMinBalance = 0;
            $dailyMaxBalance = 0;

//             // *********************************************************************************
//             // *********************************************************************************
//             // *********************************************************************************
//             // ISTO É DEMASIADO COMPLICADO PARA IR PARA A CLASSE GAIN? TEM DE IR PARA ALGUM LADO
//             // *********************************************************************************
//             // *********************************************************************************
//             // *********************************************************************************

//             // $updateGains = 0;
//             // if($updateGains) {
//             //     $sql2 = 'select id from gains where date="' . $date . '"';
//             //     $res2 = $this->body->db->query($sql2);
//             //     if($this->body->db->num_rows()) {
//             //         $row2 = $res2->fetch_assoc();
//             //         $id = $row2['id'];
//             //         $sql2 = 'update gains set min=' . $this->dailyMinBalance . ', max=' . $this->dailyMaxBalance . ' where id=' . $id;
//             //     } else {
//             //         $sql2 = 'insert into gains set date="' . $date . '", min=' . $this->dailyMinBalance . ', max=' . $this->dailyMaxBalance;
//             //     }
//             //     $this->body->db->query($sql2);
//             // }
            
//             // *********************************************************************************
//             // *********************************************************************************
//             // *********************************************************************************
//             // ISTO É DEMASIADO COMPLICADO PARA IR PARA A CLASSE GAIN? TEM DE IR PARA ALGUM LADO
//             // *********************************************************************************
//             // *********************************************************************************
//             // *********************************************************************************

            $sql2 = 'select min, max from gains where date="' . $date . '"';
            $res2 = $this->body->db->query($sql2);
            $row2 = $res2->fetch_assoc();
            $dailyMinBalance = $row2['min'];
            $dailyMaxBalance = $row2['max'];
            
            $minBalance += $dailyMinBalance;
            $maxBalance += $dailyMaxBalance;

            $html .= '
                    <div class="second-level-title grid" onClick="showMatches(\'' . $date . '\')">
                        <span class="left">' . $date . '</span>
                        <span class="span5 grid">
                            <span class="span2 left">MIN</span>
                            <span class="span5 f-sb">
                                <span>Gain</span>
                                <span>' . $this->colorFormat($dailyMinBalance, 2) . '</span>
                            </span>
                            <span class="span5 f-sb">
                                <span>Balance</span>
                                <span>' . $this->colorFormat($minBalance, 2) . '</span>
                            </span>
                        </span>
                        <span></span>
                        <span class="span5 grid">
                            <span class="span2 left">MAX</span>
                            <span class="span5 f-sb">
                                <span>Gains</span>
                                <span>' . $this->colorFormat($dailyMaxBalance, 2) . '</span>
                            </span>
                            <span class="span5 f-sb">
                                <span>Balance</span>
                                <span>' . $this->colorFormat($maxBalance, 2) . '</span>
                            </span>
                        </span>
                    </div>
                    <div id="' . $date . '" class="hidden" style="display:none">
                        <div class="third-level-title grid">
                            <div class="list-detail-heading span4">
                                <span class="left">Bets</span>
                                <span>Min</span>
                                <span>Max</span>
                            </div>
                            <div class="list-detail-heading span4">
                                <span class="left">Gains</span>
                                <span>Min</span>
                                <span>Max</span>
                            </div>
                            <div class="list-detail-heading span4">
                                <span class="left">Balance</span>
                                <span>Min</span>
                                <span>Max</span>
                            </div>
                        </div>
                        <div id="inner' . $date . '"></div>
                    </div>';
        }
        
        return $html;
    }
}
?>