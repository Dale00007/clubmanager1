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
require_once "include/functions.php";
include "include/header.php";
?>

<?php
//Get page parms
function notempty($var) {
    return ($var==="0"||$var);
}

if (!isset($_GET['eloMax'])) {$eloMax=3000;} else {$eloMax=$_GET['eloMax'];}
if (isset($_GET['playerMaxGames'])) {$playerMaxGames="1";} else {$playerMaxGames="0";}
if (isset($_GET['player960'])) {$player960="1";} else {$player960="0";}
if (isset($_GET['playerSlow'])) {$playerSlow="1";} else {$playerSlow="0";}
if (!isset($_GET['eloMin'])) {$eloMin=0;} else {$eloMin=$_GET['eloMin'];}
if (!isset($_GET['currentGamesMax'])) {$currentGamesMax=999;} else {$currentGamesMax=$_GET['currentGamesMax'];}
if (isset($_GET['timeoutMax'])) {$timeoutMax=$_GET['timeoutMax'];} else {$timeoutMax=100;}
if (!isset($_GET['currentGamesMin'])) {$currentGamesMin=0;} else {$currentGamesMin=$_GET['currentGamesMin'];}
if (!isset($_GET['lastLogin'])) {$lastLoginP=""; $lastLoginDay="";
  } else {
  $lastLoginP=$_GET['lastLogin'];
  $x="-$lastLoginP Days";
  $lastLoginDay=date("y-m-d",strtotime($x));
  //$lastLoginDay=strtotime($lastLoginDay);
  //echo "lastLogin: $lastLogin, x: $x, lastLoginDay: $lastLoginDay";
  }
$matchId=$_GET['matchid'];
$team_id=$_SESSION['team_id'];

//Get teamlink based on session and update match details from Chess.com
$sql = "SELECT teamlink FROM teams WHERE id=$team_id";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$teamLink=$row['teamlink'];
updateMatchReg($matchId,$team_id,$teamLink);

echo "<body>";
include "include/navigation.php";
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
 $opponentName=$row['opponent_name'];
 $players=$row['players'];
 $playersOpp=$row['players_o'];
 $avgRat=$row['avgrat'];
 $avgRatOpp=$row['avgrat_o'];
 $basEst=$row['basest'];
 $basEstOpp=$row['basest_o'];
 $advEst=$row['advest'];
 $advEstOpp=$row['advest_o'];
 $rules=$row['rules'];
 $timeClass=$row['time_class'];
 $timeControl=$row['time_control_day'];
 $started=$row['started'];
 $minTeamPlayers=$row['min_team_players'];

 if ($timeClass=="D") {$timeClass="Daily";} else {$timeClass="Live";}
 if ($rules=="S") {$rules="Standard"; $eloSort="elo_s";} else {$rules="Chess960"; $eloSort="elo_960";}

echo "<div align='left' class='container'>
<h3>Match Summary</h3>
<TABLE align='left'>
 <TR><TH>Match ID</TH><TD><A HREF='https://www.chess.com/club/matches/$matchId/games' target='_blank' class='tablea'>$matchId</A></TD></TR>
 <TR><TH>Match API</TH><TD><A HREF='https://api.chess.com/pub/match/$matchId' target='_blank' class='tablea'>https://api.chess.com/pub/match/$matchId</A></TD></TR>
 <TR><TH>Match Name</TH><TD>$matchName</TD></TR>
 <TR><TH>Opponent</TH><TD><A href='https://www.chess.com/club/$opponentLink' target='_blank' class='tablea'>$opponentName</A></TD></TR>
 <TR><TH>Start date</TH><TD>$started</TD></TR>
 <TR><TH>Rules</TH><TD>$rules</TD></TR>
 <TR><TH>Time class</TH><TD>$timeClass</TD></TR>
 <TR><TH>Time control</TH><TD>$timeControl</TD></TR>
 <TR><TH>Min players</TH><TD>$minTeamPlayers</TD></TR>
 <TR><TH>Players</TH><TD align='center'>$players : $playersOpp</TD></TR>
 <TR><TH>Average</TH><TD align='center'>$avgRat : $avgRatOpp</TD></TR>
 <TR><TH>Basic Estimate</TH><TD align='center'>$basEst : $basEstOpp</TD></TR>
 <TR><TH>Advanced Estimate</TH><TD align='center'>$advEst : $advEstOpp</TD></TR>
 <TR><TH colspan='2'><A HREF='matches_registration.php?deleteMatchId=$matchId' class='tablea'>DELETE MATCH</A></TH></TR>

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
 <TH>Details</TH>
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
     $playerTimeout = $row->timeout_percent;
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
     $playerTimeout = $row->timeout_percent;
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
    <TD align='center'><A HREF='players_player_details.php?pName=$pName' class='tablea' target='_blank'>ENTER</A></TD>
    </TR>";
  }
}
?>

</TABLE>
<br clear='all' /><br />

<h3>Candidate players</h3>
<form action='matches_registration_match.php' method='get'>

<?php
echo "<INPUT type='hidden' name='matchid' value=$matchId>
<TABLE align='left'>
  <THEAD>
  <TR><TH colspan=3>Filtering criteria</TH></TR>
  </THEAD>
  <TR><TD>ELO</TD>
      <TD align='center'><INPUT type='text' name='eloMin' value='$eloMin' size=4></TD>
      <TD align='center'><INPUT type='text' name='eloMax' value='$eloMax' size=4></TD>
  </TR>
  <TR><TD>Last login not older than</TD><TD colspan=2 align='center'><SELECT name='lastLogin'>
      <OPTION value=''"; if ($lastLoginP == "") {echo " SELECTED";}; echo ">Not used</OPTION>
      <OPTION value='3'"; if ($lastLoginP == "3") {echo " SELECTED";}; echo ">3 days</OPTION>
      <OPTION value='7'"; if ($lastLoginP == "7") {echo " SELECTED";}; echo ">7 days</OPTION>
      <OPTION value='14'"; if ($lastLoginP == "14") {echo " SELECTED";}; echo ">14 days</OPTION>
      <OPTION value='30'"; if ($lastLoginP == "30") {echo " SELECTED";}; echo ">30 days</OPTION></SELECT>
      </TD></TR>
  <TR><TD>Max timeout</TD><TD colspan=2 align='center'><INPUT type='text' name='timeoutMax' value='$timeoutMax' size=4></TD></TR>
  <TR><TD>Current games</TD>
      <TD align='center'><INPUT type='text' name='currentGamesMin' value='$currentGamesMin' size=4></TD>
      <TD align='center'><INPUT type='text' name='currentGamesMax' value='$currentGamesMax' size=4></TD></TR>
  <TR><TD>Max games</TD><TD colspan=2>respect player level parameter <INPUT type='checkbox' name='playerMaxGames'";
  if ($playerMaxGames=="1") {echo " checked";}
  echo "></TD></TR>
  <TR><TD>Chess960</TD><TD colspan=2>respect player level parameter <INPUT type='checkbox' name='player960'";
  if ($player960=="1") {echo " checked";}
  echo "></TD></TR>
  <TR><TD>Only slow games</TD><TD colspan=2>respect player level parameter <INPUT type='checkbox' name='playerSlow'";
  if ($playerSlow=="1") {echo " checked";}
  echo "></TD></TR>
  ";
?>
  <THEAD>
  <TR><TH colspan=3><INPUT type='submit' value='FILTER'></TH></TR>
  </THEAD>
</TABLE>
</FORM>
<br clear='all' /><br />
<div align="left" class="container">
<TABLE align="left" class="sortable">
 <THEAD>
 <TR>
 <TH>Playername</TH>
 <TH>Msg</TH>
 <TH>Rating</TH>
 <TH>Rat960</TH>
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
       AND $eloSort>=$eloMin
       AND $eloSort<=$eloMax
       AND to_ratio_site<=$timeoutMax
       AND current_games>=$currentGamesMin
       AND current_games<=$currentGamesMax ";
       if (!empty($lastLoginDay)) {$sql=$sql."AND last_login>'$lastLoginDay' ";}
       if ($playerMaxGames=="1") {$sql=$sql."AND current_games<=parm_max_games ";}
       if ($player960=="1") {$sql=$sql."AND not parm_960 ";}
       if ($playerSlow=="1") {$sql=$sql."AND not parm_slow ";}

 $sql=$sql."ORDER BY $eloSort DESC";
 //echo "$sql<BR>";
 $result = $link->query($sql);
$i=0;
while ($row = $result->fetch_assoc()) {
   $pName=$row['username'];
   $pRating=$row['elo_s'];
   $pRating960=$row['elo_960'];
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
      <TD align='center'><A HREF='https://www.chess.com/messages/compose/$pName' target='_blank' class='tablea'>MSG</A></TD>
      <TD align='center'>$pRating</TD>
      <TD align='center'>$pRating960</TD>
      <TD align='center'>$pTimeout</TD>
      <TD align='center'>$pLastLogin</TD>
      <TD align='center'>$pCurrentGames</TD>
      <TD align='center'>$ppMaxGames</TD>
      <TD align='center'>$pp960</TD>
      <TD align='center'>$ppSlow</TD>
      <TD>$ppComment</TD>
      <TD align='center'><A HREF='players_player_details.php?pName=$pName' class='tablea' target='_blank'>ENTER</A></TD>
      </TR>";
      $availableCandidates[$i]=$pName;
      $i++;
      }
    }
    echo "</TABLE><br clear='all' /><br /><HR>";
    foreach ($availableCandidates as $key => $value) {
      echo "$value<br>";
      }
 ?>

<?php
include "include/navigationend.php";
?>

</body>
</html>
