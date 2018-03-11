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
$pName=$_GET['pName'];
$team_id=$_SESSION['team_id'];

if (isset($_GET['ptid'])) {
  $pmaxg=$_GET['pmaxg'];
  if (isset($_GET['pslow'])) {$pslow="1";} else {$pslow="0";}
  if (isset($_GET['p960'])) {$p960="1";} else {$p960="0";}
  $ptid=$_GET['ptid'];
  $pcomment=$_GET['pcomment'];
  $sql="UPDATE players_teams
  SET parm_max_games=$pmaxg, parm_slow=$pslow, parm_960=$p960, parm_comment='$pcomment'
  WHERE id=$ptid";
  echo "$sql<BR>";
  $result = $link->query($sql);
}

echo "<body>";
include "include/navigation.php";
?>

<P>
<h2>Club player details</h2>
<h3>Summary</h3>
<?php
$sql = "SELECT *, players_teams.id as ptid
FROM players, players_teams
WHERE players_id=players.id
AND teams_id=$team_id
AND username='$pName'";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$ptid=$row['ptid'];
$mf=$row['member_from'];
$ll=$row['last_login'];
$jt=$row['joined_team'];
$cg=$row['current_games'];
$ms=$row['matches_s'];
$ps=$row['points_s'];
$ags=$row['active_matches_s'];
$m9=$row['matches_9'];
$p9=$row['points_9'];
$ag9=$row['active_matches_9'];
$to=$row['timeouts'];
$lastto=$row['last_timeout'];
$tos=$row['to_ratio_site'];
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
echo "<TABLE align='left'>
 <TR><TH>Player name</TH><TD><A HREF='https://www.chess.com/member/$pName' target='_blank' class='tablea'>$pName</A></TD></TR>
 <TR><TH>Player API</TH><TD><A HREF='https://api.chess.com/pub/player/$pName' target='_blank' class='tablea'>https://api.chess.com/pub/player/$pName</A></TD></TR>
 <TR><TH>Location</TH><TD>$location, $country</TD></TR>
 <TR><TH>Member from</TH><TD>$mf</TD></TR>
 <TR><TH>Last login</TH><TD>$ll</TD></TR>
 <TR><TH>Timeout - site, club</TH><TD>$to / $tos</D></TR>
 <TR><TH>Last timeout</TH><TD>$lastto</TD></TR>
 <TR><TH>Curreng games at site</TH><TD>$cg</TD></TR>
 </TABLE></P>

<br clear='all' /><br />
<h3>Parameters</h3>
<form action='players_player_details.php' method='get'>
<INPUT type='hidden' name='pName' value='$pName'>
<INPUT type='hidden' name='ptid' value='$ptid'>
<TABLE align='left'>
 <TR><TH>Is not interested in Chess960</TH><TD>$p960</TD><TD><INPUT type='checkbox' name='p960' value='$p960'";
 if ($p960=="1") {echo " checked";}
 echo "></TD></TR>
 <TR><TH>Plays only slow games</TH><TD>$pslow</TD><TD><INPUT type='checkbox' name='pslow' value='$pslow'";
 if ($pslow=="1") {echo " checked";}
 echo "></TD></TR>
 <TR><TH>Maximum of the games at one time</TH><TD>$pmaxg</TD><TD><INPUT type='text' name='pmaxg' value='$pmaxg' size=3></TD></TR>
 <TR><TH>Comment</TH><TD>$pcomment</TD><TD><INPUT type='text' name='pcomment' value='$pcomment' size=50></TD><</TR>
  <TR><TH colspan=3><INPUT type='submit' value='UPDATE'></TH></TR>
 </TABLE></P>
</form>";
 ?>

<br clear='all' /><br />
<h3>List of club timeouts</h3>

<br clear='all' /><br />
<h3>List of club games</h3>

<br clear='all' /><br />
<h3>Overall Timeout Stat</h3>

<?php

 ?>

<?php
include "include/navigationend.php";
?>

</body>
</html>
