<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}

// Include config file
require_once 'include/config.php';

include "include/header.php";
?>




<?php
echo "<body>";
include "include/navigation.php";
?>

<h2>List of players based on Ratings</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH>Elo<SPAN class="tooltip">Elo for daily chess</SPAN></TH>
	   <TH>E960<SPAN class="tooltip">Elo for daily chess - Chess960</SPAN></TH>
	   <TH>EloBu<SPAN class="tooltip">Elo for bullet</SPAN></TH>
	   <TH>EloBl<SPAN class="tooltip">Elo for blitz</SPAN></TH>
	   <TH>EloR<SPAN class="tooltip">Elo for rapid</SPAN></TH>
	   <TH>Tac<SPAN class="tooltip">Elo for Tactics - Best</SPAN></TH>
	   <TH>Les<SPAN class="tooltip">Elo for Lessons - Best</SPAN></TH>
	   <TH>PRSc<SPAN class="tooltip">Puzzle Rush - Score</SPAN></TH>	
	   <TH>PRAt<SPAN class="tooltip">Puzzle Rush - Attempts</SPAN></TH>		   
       <TH>Details<SPAN class="tooltip">Player page with details</SPAN></TH>
     </TR>
     </THEAD>
<?php
$team_id=$_SESSION['team_id'];
$sql = "SELECT *
		FROM players,players_teams
		WHERE players.id=players_teams.players_id
			AND teams_id=$team_id
			AND NOT left_team
			AND players_status_id=1
		ORDER BY username";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $username=$row['username'];
 $mf=$row['member_from'];
 $ll=$row['last_login'];
 $cg=$row['current_games'];
 $ms=$row['matches_s'];
 $ps=$row['points_s'];
 $ags=$row['active_matches_s'];
 $m9=$row['matches_9'];
 $p9=$row['points_9'];
 $ag9=$row['active_matches_9'];
 $to=$row['timeouts'];
 $lastto=$row['last_timeout'];
 $p_mg=$row['parm_max_games'];
 $p_slow=$row['parm_slow'];
 $p_960=$row['parm_960'];
 $p_com=$row['parm_comment'];
 $country=$row['country'];
 $location=$row['location'];
 $elos=$row['elo_s'];
 $elo960=$row['elo_960'];
 $elor=$row['elo_rapid'];
 $elob=$row['elo_blitz'];
 $elobu=$row['elo_bullet'];
 $elot=$row['elo_t'];
 $elol=$row['elo_lessons'];
 $eloprsc=$row['elo_rush_score'];
 $eloprat=$row['elo_rush_attempts'];
 $pmaxg=$row['parm_max_games'];
 $p960=$row['parm_960'];
 $pslow=$row['parm_slow'];
 $pcomment=$row['parm_comment'];
 $m=$m9+$ms;
	$ag=$ag9+$ags;
	$p=$p9+$ps;

 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$elobu</TD>
  <TD align='center'>$elob</TD>
  <TD align='center'>$elor</TD>
  <TD align='center'>$elot</TD>
  <TD align='center'>$elol</TD>
  <TD align='center'>$eloprsc</TD>
  <TD align='center'>$eloprat</TD>
  <TD align='center'><A HREF='players_player_detail.php?pName=$username' class='tablea' target='_blank'>ENTER</A></TD>
  </TR>";
}
?>

<?php
include "include/navigationend.php";
?>
</body>
</html>
