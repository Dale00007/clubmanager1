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

<h2>List of players</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Games<SPAN class="tooltip">Games completed for the club (standard/960)</SPAN></TH>
       <TH>Points<SPAN class="tooltip">Points from team games (standard/960)</SPAN></TH>
	   <TH>Active<SPAN class="tooltip">Active games for the team (standard/960)</SPAN></TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH width=80>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=80>Member<BR>from<SPAN class="tooltip"></SPAN></TH>
	   <TH>TO<SPAN class="tooltip">Number of timeouts in the team</SPAN></TH>
	   <TH width=120>Last TO<SPAN class="tooltip">Date of the last timeout</SPAN></TH>
	   <TH>Elo<SPAN class="tooltip">Elo for daily chess - standard</SPAN></TH>
	   <TH>E960<SPAN class="tooltip">Elo for daily chess - Chess960</SPAN></TH>
	   <TH>EloB<SPAN class="tooltip">Elo for blitz</SPAN></TH>
	   <TH>EloR<SPAN class="tooltip">Elo for rapid</SPAN></TH>
       <TH>Ctry<SPAN class="tooltip">Country from Chess.com</SPAN></TH>
	   <TH width=80>Location<SPAN class="tooltip">Location from Chess.com</SPAN></TH>
       <TH>P-MaxG<SPAN class="tooltip">Maximum daily games player accepts</SPAN></TH>
       <TH>P-960<SPAN class="tooltip">Player doesn't want to play Chess960</SPAN></TH>
       <TH>P-Slow<SPAN class="tooltip">Player plays only games with pace slower than 3 days</SPAN></TH>
       <TH>P-Comment<SPAN class="tooltip">Comment about the player</SPAN></TH>
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
 $pmaxg=$row['parm_max_games'];
 $p960=$row['parm_960'];
 $pslow=$row['parm_slow'];
 $pcomment=$row['parm_comment'];
 $m=$m9+$ms;
	$ag=$ag9+$ags;
	$p=$p9+$ps;

 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$m ($ms/$m9)</TD>
  <TD align='center'>$p ($ps/$p9)</TD>
  <TD align='center'>$ag ($ags/$ag9)</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$to</TD>
  <TD align='center'>$lastto</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$elor</TD>
  <TD align='center'>$elob</TD>
  <TD align='center'>$country</TD>
  <TD style='word-wrap:break-word'>$location</TD>
  <TD align='center'>$pmaxg</TD>
  <TD align='center'>$p960</TD>
  <TD align='center'>$pslow</TD>
  <TD align='center'>$pcomment</TD>
  </TR>";
}
?>

<?php
include "include/navigationend.php";
?>
</body>
</html>
