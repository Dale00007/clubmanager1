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
	
<h2>List of left players in last 30 days</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH width=120>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=160>Joined club<SPAN class="tooltip">When player was found as club member</SPAN></TH>
	   <TH width=160>Left club<SPAN class="tooltip">When player has left club</SPAN></TH>	 
	   <TH width=120>Member<BR>from<SPAN class="tooltip"></SPAN></TH>
	   <TH>Elo<SPAN class="tooltip">Elo for daily chess - standard</SPAN></TH>
	   <TH>E960<SPAN class="tooltip">Elo for daily chess - Chess960</SPAN></TH>
       <TH>Ctry<SPAN class="tooltip">Country from Chess.com</SPAN></TH>
	   <TH>Location<SPAN class="tooltip">Location from Chess.com</SPAN></TH>		   
     </TR>
     </THEAD>
<?php
$team_id=$_SESSION['team_id'];
$sql = "SELECT * 
		FROM players,players_teams 
		WHERE players.id=players_teams.players_id 
			AND teams_id=$team_id 
			AND left_team
			AND players_status_id=1
			AND left_team_date > NOW() - INTERVAL 30 DAY 
		ORDER BY left_team_date DESC";
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
 $jt=$row['joined_team'];	
 $p_mg=$row['parm_max_games'];	
 $p_slow=$row['parm_slow'];	
 $p_960=$row['parm_960'];	
 $p_com=$row['parm_comment'];	
 $country=$row['country'];	
 $location=$row['location'];
 $elos=$row['elo_s'];
 $elo960=$row['elo_960'];
 $pmaxg=$row['parm_max_games'];	
 $p960=$row['parm_960'];
 $pslow=$row['parm_slow'];
 $pcomment=$row['parm_comment'];
 $lt=$row['left_team_date'];
 $m=$m9+$ms;
	$ag=$ag9+$ags;
	$p=$p9+$ps;
	
 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$jt</TD>
  <TD align='center'>$lt</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$country</TD>
  <TD>$location</TD>
  </TR>";
}
?>
		
<?php
include "include/navigationend.php";
?>
</body>
</html>