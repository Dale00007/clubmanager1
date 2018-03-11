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

<h2>List of completed matches</h2>
<div align="left" class="container">
<TABLE align="left" class="sortable">
 <THEAD>
 <TR>
   <TH>MatchID</TH>
   <TH>Match Name</TH>
   <TH>Opponent</TH>
   <TH>Boards</TH>
   <TH>Score</TH>
   <TH width=120>Competition</TH>

   <TH>Details<SPAN class="tooltip">Displays page with detailed match information for recruitment</SPAN></TH>
 </TR>
 </THEAD>

 <?php
 $sql = "SELECT *
 		FROM matches, gametype_seasons
 		WHERE status=2
 			AND teams_id=$team_id
			AND matches.competition=gametype_seasons.id
 		ORDER BY matchid";
 $result = $link->query($sql);
 while ($row = $result->fetch_assoc()) {
  $matchId=$row['matchid'];
  $matchName=$row['matchname'];
  $opponentLink=$row['opponent_link'];
  $opponentName=$row['opponent_name'];
  $boards=$row['boards'];
  $score=$row['score'];
  $opponentScore=$row['score_opp'];
  $competition=$row['abbr'];

  echo "<TR>
   <TD align='center' width=80><A href='https://www.chess.com/club/matches/$matchId' target='_blank' class='tablea'>$matchId</TD>
   <TD align='left' width=200>$matchName</TD>
   <TD align='left' width=200><A href='https://www.chess.com/club/$opponentLink' target='_blank' class='tablea'>$opponentName</A></TD>
   <TD align='center'>$boards</TD>
   <TD align='center'>$score:$opponentScore</TD>
   <TD align='center'>$competition</TD>
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
