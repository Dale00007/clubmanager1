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
include "include/functions.php";
$team_id=$_SESSION['team_id'];
$sql = "SELECT teamlink FROM teams WHERE id=$team_id";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$team_link=$row['teamlink'];
$findType="registered";
findClubMatches($team_link,$team_id,$findType);

?>

<h2>List of matches in registration</h2>
<div align="left" class="container">
<TABLE align="left" class="sortable">
 <THEAD>
 <TR>
   <TH width=80>MatchID</TH>
   <TH width=300>Match Name</TH>
   <TH>Opponent</TH>
   <TH>Start</TH>
   <TH>Players<SPAN class="tooltip">Number of registered players</SPAN></TH>
   <TH>Average<SPAN class="tooltip">Average rating of players</SPAN></TH>
   <TH>BasEst<SPAN class="tooltip">Estimated result based on direct comparison of ratings on boards</SPAN></TH>
   <TH>AdvEst<SPAN class="tooltip">Estimated result based on advanced comparison of ratings on the boards</SPAN></TH>
   <TH>Details<SPAN class="tooltip">Displays page with detailed match information for recruitment</SPAN></TH>
 </TR>
 </THEAD>

 <?php
 $sql = "SELECT matchid
     FROM matches
     WHERE status=0
       AND teams_id=$team_id
     ORDER BY started ASC";
 $result = $link->query($sql);
 while ($row = $result->fetch_assoc()) {
     $matchId=$row['matchid'];
     updateMatchReg($matchId,$team_id,$team_link);
 }

 $sql = "SELECT *
 		FROM matches
 		WHERE status=0
 			AND teams_id=$team_id
 		ORDER BY started ASC";
 $result = $link->query($sql);
 while ($row = $result->fetch_assoc()) {
  $matchId=$row['matchid'];
  $matchName=$row['matchname'];
  $opponentLink=$row['opponent_link'];
  $opponentName=$row['opponent_name'];
  $players=$row['players'];
  $playersOpp=$row['players_o'];
  $avgRat=$row['avgrat'];
  $avgRatOpp=$row['avgrat_o'];
  $basEst=$row['basest'];
  $basEstOpp=$row['basest_o'];
  $advEst=$row['advest'];
  $advEstOpp=$row['advest_o'];
  $started=$row['started'];
  $minTeamPlayers=$row['min_team_players'];
  if ($players>=$minTeamPlayers) {$minPlayersCheck="white";} else {$minPlayersCheck="red";}
  if ($playersOpp>=$minTeamPlayers) {$minPlayersCheckOpp="white";} else {$minPlayersCheckOpp="red";}
  $today=date('Y-m-d');
  $started2 = date('Y-m-d',strtotime($started));
  $days2start = (strtotime($started2) - strtotime($today))/60/60/24;

  echo "<TR>
   <TD align='center'><A href='https://www.chess.com/club/matches/$matchId' target='_blank' class='tablea'>$matchId</TD>
   <TD align='left'>$matchName</TD>
   <TD align='left'><A href='https://www.chess.com/club/$opponentLink' target='_blank' class='tablea'>$opponentName</A></TD>
   <TD align='center'>$started<BR>$days2start days</TD>
   <TD align='center'><font color='$minPlayersCheck'>$players</fong> : <font color='$minPlayersCheckOpp'>$playersOpp</font></TD>
   <TD align='center'>$avgRat : $avgRatOpp</TD>
   <TD align='center'>$basEst : $basEstOpp</TD>
   <TD align='center'>$advEst : $advEstOpp</TD>
   <TD align='center'><A href='matches_registration_match.php?matchid=$matchId' class='tablea'>ENTER</A></TD>
   </TR>";
 }
  ?>

  </TABLE>

<?php
include "include/navigationend.php";
?>

</body>
</html>
