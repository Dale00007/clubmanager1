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
$matchId=$_GET['matchid'];
$sql = "SELECT teamlink FROM teams WHERE id=$team_id";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$teamLink=$row['teamlink'];
updateMatchReg($matchId,$team_id,$teamLink);
?>
<P>
<h2>Match details - Registration phase</h2>

<?php
$sql = "SELECT *
   FROM matches
   WHERE matchid=$matchId";
$result = $link->query($sql);
 $row = $result->fetch_assoc();
 $matchName=$row['matchname'];
 $opponentLink=$row['opponent_link'];
 $players=$row['players'];
 $playersOpp=$row['players_o'];
 $avgRat=$row['avgrat'];
 $avgRatOpp=$row['avgrat_o'];
 $basEst=$row['basest'];
 $basEstOpp=$row['basest_o'];
 $advEst=$row['advest'];
 $advEstOpp=$row['advest_o'];
echo "<div align='left' class='container'>
<h3>Match Summary</h3>
<TABLE align='left'>
 <TR><TH>Match ID</TH><TD><A HREF='https://www.chess.com/club/matches/$matchId/games' target='_blank' class='tablea'>$matchId</A></TD></TR>
 <TR><TH>Match API</TH><TD><A HREF='https://api.chess.com/pub/match/$matchId' target='_blank' class='tablea'>https://api.chess.com/pub/match/$matchId</A></TD></TR>
 <TR><TH>Match Name</TH><TD>$matchName</TD></TR>
 <TR><TH>Opponent</TH><TD>$opponentLink</TD></TR>
 <TR><TH>Players</TH><TD align='center'>$players : $playersOpp</TD></TR>
 <TR><TH>Average</TH><TD align='center'>$avgRat : $avgRatOpp</TD></TR>
 <TR><TH>Basic Estimate</TH><TD align='center'>$basEst : $basEstOpp</TD></TR>
 <TR><TH>Advanced Estimate</TH><TD align='center'>$advEst : $advEstOpp</TD></TR>
 </TABLE></P>
 <br clear='all' /><br />";
?>
<P>
<h3>Registered players</h3>
<div align="left" class="container">
<TABLE align="left" class="sortable">
 <THEAD>
 <TR>
 <TH>Playername</TH>
 <TH>Rating</TH>
 <TH>Opponent</TH>
 <TH>OppRat<SPAN class="tooltip">Rating of the opponent</SPAN></TH>
 <TH>Timeout<SPAN class="tooltip">Percentage of timeouts in last 3months based on Chess.com</SPAN></TH>
 <TH>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
 <TH>CG<SPAN class="tooltip">Number of online games player is having now</SPAN></TH>
 </TR>
 </THEAD>

<?php
$url="https://api.chess.com/pub/match/$matchId";
$url = strtolower($url);
$json1=getJson($url);
$json=$json1[0];
$htpperror=$json1[1];
$obj = json_decode($json);
if ($htpperror<>200) {
    $sql="INSERT INTO api_errors (url,error)
    VALUES('$url',$htpperror)";
  //echo $sql.'<BR>';
  $result = $link->query($sql);
  echo "Problem with loading of match from Chess.com - Please try again later.";
} else {
  $teams = $obj->teams;
  $team1 = $teams->team1;
  $team1LinkLong = $team1->{'@id'};
  $team1Link = substr($team1LinkLong,31,strlen($team1LinkLong)-31);
  $team2 = $teams->team2;
  $team2LinkLong = $team2->{'@id'};
  $team2Link = substr($team2LinkLong,31,strlen($team2LinkLong)-31);
  if ($team1Link==$teamLink) {
    $homeaway="home";
  } else {
    $homeaway="away";
  }
  $players1 = $team1->players;
  $players2 = $team2->players;
  $plcnt1=count($players1);
  $plcnt2=count($players2);
  $currentBoards = min($plcnt1,$plcnt2);
  $oa=(array) $players1;
  $i=0;
	foreach($oa as $row){
     $playerName = $row->username;
     if (empty($row->rating)){$playerRating=""; } else {$playerRating = $row->rating;}
     $playerTimeout = $row->timeout;
     if ($playerRating<>""){
     $teamPlayers1[$i]=array("playername" => $playerName,"rating" => $playerRating, "timeout" => $playerTimeout);
     $i++;
     }
  }
  $oa=(array) $players2;
  $i=0;
  foreach($oa as $row){
     $playerName = $row->username;
     if (empty($row->rating)){$playerRating=""; } else {$playerRating = $row->rating;}
     $playerTimeout = $row->timeout;
     if ($playerRating<>""){
     $teamPlayers2[$i]=array("playername" => $playerName,"rating" => $playerRating, "timeout" => $playerTimeout);
     $i++;
   }
  }
  function cmpdesc($a, $b)
  {
    if ($a["rating"]<$b["rating"]) {return true;} else {return false;}
  }
  usort($teamPlayers1, 'cmpdesc');
  usort($teamPlayers2, 'cmpdesc');
  if ($team1Link==$teamLink) {
    $homeaway="home";
    $homeTeam=$teamPlayers1;
    $awayTeam=$teamPlayers2;
  } else {
    $homeaway="away";
    $homeTeam=$teamPlayers2;
    $awayTeam=$teamPlayers1;
  }
  $oNames = array_column($awayTeam, 'playername');
  $oRatings = array_column($awayTeam, 'rating');

  $i=0;
  foreach ($homeTeam as $row) {
    $pName=$row['playername'];
    $pRating=$row['rating'];
    $pTimeout=$row['timeout'];
    if (empty($oNames[$i])){$oName=""; } else {$oName=$oNames[$i];}
    if (empty($oRatings[$i])){$oRatings=""; } else {$oRating=$oRatings[$i];}
    $sql = "SELECT last_login, current_games
          FROM players, players_teams
          WHERE players_teams.teams_id=$team_id
          AND players.username='$pName'";
    $result1 = $link->query($sql);
    $row1 = $result1->fetch_assoc();
    $lastLogin=$row1['last_login'];
    $currentGames=$row1['current_games'];
    $registeredPlayers[$i]=$pName;
    $i++;
    echo "<TR>
    <TD><A HREF='https://www.chess.com/member/$pName' target='_blank' class='tablea'>$pName</A></TD>
    <TD align='center'>$pRating</TD>
    <TD>$oName</TD>
    <TD align='center'>$oRating</TD>
    <TD align='center'>$pTimeout</TD>
    <TD align='center'>$lastLogin</TH>
    <TD align='center'>$currentGames</TH>
    </TR>";
  }
}
?>

</TABLE>
<br clear='all' /><br />

<h3>Candidate players</h3>
<div align="left" class="container">
<TABLE align="left" class="sortable">
 <THEAD>
 <TR>
 <TH>Playername</TH>
 <TH>Rating</TH>
 <TH>TO<SPAN class="tooltip">Percentage of timeouts in last 3months based on Chess.com</SPAN></TH>
 <TH>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
 <TH>CG<SPAN class="tooltip">Number of online games player is having now</SPAN></TH>
 <TH>Max<SPAN class="tooltip">Maximum number of online games acceptable by the player</SPAN></TH>
 <TH>960<SPAN class="tooltip">Player doesnt want to play 960</SPAN></TH>
 <TH>Slow<SPAN class="tooltip">Player want to play only slow games 7days per move and slower</SPAN></TH>
 <TH>Comment<SPAN class="tooltip">Free text comment about the player</SPAN></TH>
 <TH>Details<SPAN class="tooltip">Detailed information about the player and update of his details - Opens in the new windown</SPAN></TH>

 </TR>
 </THEAD>

<?php
 $sql = "SELECT username, last_login, current_games, to_ratio_site, elo_s, elo_960, parm_max_games, parm_960, parm_slow, parm_comment
       FROM players, players_teams
       WHERE players_teams.teams_id=$team_id
       AND players_teams.players_id=players.id
       AND players_status_id=1
       AND not left_team
       AND not block
       ORDER BY elo_s DESC";
 $result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
   $pName=$row['username'];
   $pRating=$row['elo_s'];
   $pTimeout=$row['to_ratio_site'];
   $pLastLogin=$row['last_login'];
   $pCurrentGames=$row['current_games'];
   $ppMaxGames=$row['parm_max_games'];
   $pp960=$row['parm_960'];
   $ppSlow=$row['parm_slow'];
   $ppComment=$row['parm_comment'];

   if(!in_array($pName,$registeredPlayers)) {
     echo "<TR>
      <TD><A HREF='https://www.chess.com/member/$pName' target='_blank' class='tablea'>$pName</A></TD>
      <TD align='center'>$pRating</TD>
      <TD align='center'>$pTimeout</TD>
      <TD align='center'>$pLastLogin</TD>
      <TD align='center'>$pCurrentGames</TD>
      <TD align='center'>$ppMaxGames</TD>
      <TD align='center'>$pp960</TD>
      <TD align='center'>$ppSlow</TD>
      <TD>$ppComment</TD>
      <TD><A HREF='players_player_detail.php' class='tablea'>ENTER</A></TD>
      </TR>";
      }
    }
 ?>

</TABLE>
<?php
include "include/navigationend.php";
?>

</body>
</html>
