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

<h2>Filtered list of players for inviting</h2>

<?php
$team_id=$_SESSION['team_id'];
$invite_type=$_GET["invite_type"];
$markall = isset($_GET['markall']) ? $_GET['markall'] : '';
echo "<h3>Type: $invite_type</h3>";
$p_retry_m=3;
$x="-$p_retry_m Months";
$retryday=date("y-m-d",strtotime($x));
$retryday= strtotime($retryday);
$dnes=date ("Y-m-d");

if ($markall == "yes") {
  switch ($invite_type) {
      case "current_games":
          $sql = "SELECT *, candidates_teams.id AS cid
  		FROM candidates,candidates_teams
  		WHERE candidates.id=candidates_teams.candidates_id
  			AND teams_id=$team_id
  			AND NOT block
        AND last_invite<'$retryday'
  		ORDER BY current_games DESC, elo_s DESC, elo_blitz DESC, elo_rapid DESC, name
  		LIMIT 30";
          break;
      case "elo":
          $sql = "SELECT *, candidates_teams.id AS cid
  		FROM candidates,candidates_teams
  		WHERE candidates.id=candidates_teams.candidates_id
  			AND teams_id=$team_id
  			AND NOT block
        AND last_invite<'$retryday'
  		ORDER BY elo_s DESC, current_games DESC, elo_blitz DESC, elo_rapid DESC, name
  		LIMIT 30";
          break;
  }
  $result = $link->query($sql);
  while ($row = $result->fetch_assoc()) {
   $cid=$row['cid'];
   $sqlupd2="UPDATE candidates_teams SET last_invite='$dnes' WHERE id=$cid";
   $res2=$link->query($sqlupd2);
   }
}

if(isset($_GET['delay'])) {
$delay=$_GET['delay'];
		$z=0;
		while($z < count($delay)) {
	        $ii=$z;
          $sqlupd2="UPDATE candidates_teams SET last_invite='$dnes' WHERE id=$delay[$ii]";
      	 	$res2=$link->query($sqlupd2);
         	//echo "$sqlupd2 <BR>"; $sqlupd2="";
         	$z++;
   }
}

if(isset($_GET['blk'])) {
$blk=$_GET['blk'];
		$z=0;
		while($z < count($blk)) {
	        $ii=$z;
          $sqlupd2="UPDATE candidates_teams SET block=true WHERE id=$blk[$ii]";
      	 	$res2=$link->query($sqlupd2);
         	$z++;
   }
}

?>

<H4>Players for the invite</H4>

<?php
switch ($invite_type) {
    case "current_games":
        $sql = "SELECT *, candidates_teams.id AS cid
		FROM candidates,candidates_teams
		WHERE candidates.id=candidates_teams.candidates_id
			AND teams_id=$team_id
			AND NOT block
      AND last_invite<'$retryday'
		ORDER BY current_games DESC, elo_s DESC, elo_blitz DESC, elo_rapid DESC, name
		LIMIT 30";
        break;
    case "elo":
        $sql = "SELECT *, candidates_teams.id AS cid
		FROM candidates,candidates_teams
		WHERE candidates.id=candidates_teams.candidates_id
			AND teams_id=$team_id
			AND NOT block
      AND last_invite<'$retryday'
		ORDER BY elo_s DESC, current_games DESC, elo_blitz DESC, elo_rapid DESC, name
		LIMIT 30";
        break;
}


$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $username=$row['name'];
 echo "$username, ";
 }
?>

<HR>
<H4>Text for the invite</H4>

<?php
$tid=$_SESSION['team_id'];
$sql="SELECT p_invitetext FROM teams WHERE id=$tid";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$teamtext=$row['p_invitetext'];
echo $teamtext;
?>
<HR>

<?php
echo "<H4 align='center'><A href='recruitment_invite.php?invite_type=$invite_type&markall=yes'>
Mark ALL players on this list as INVITED</A></H4>";
?>
	<form action='recruitment_invite.php' method='get'>
    <div align="left" class="container">
    <TABLE align="left" class="sortable" width="100%">
     <THEAD>
     <TR>
     <TH>Block<SPAN class="tooltip">Will remove player from candidates list for your club</SPAN></TH>
     <TH>Delay<SPAN class="tooltip">Will update last invite date as today without sending invite</SPAN></TH>
     <TH>Username</TH>
	   <TH width=80>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=80>Member<BR>from<SPAN class="tooltip"></SPAN></TH>
	   <TH>Current<BR>games<SPAN class="tooltip">Number of daily games ongoing</SPAN></TH>
	   <TH>Elo<SPAN class="tooltip">Elo for daily chess - standard</SPAN></TH>
	   <TH>E960<SPAN class="tooltip">Elo for daily chess - Chess960</SPAN></TH>
	   <TH>EloB<SPAN class="tooltip">Elo for blitz</SPAN></TH>
	   <TH>EloR<SPAN class="tooltip">Elo for rapid</SPAN></TH>
     <TH>Ctry<SPAN class="tooltip">Country from Chess.com</SPAN></TH>
	   <TH>Location<SPAN class="tooltip">Location from Chess.com</SPAN></TH>
	   <TH>Last invite<SPAN class="tooltip">Date of the last invite sent</SPAN></TH>

     </TR>
     </THEAD>

<?php


switch ($invite_type) {
    case "current_games":
        $sql = "SELECT *, candidates_teams.id AS cid
		FROM candidates,candidates_teams
		WHERE candidates.id=candidates_teams.candidates_id
			AND teams_id=$team_id
			AND NOT block
      AND last_invite<'$retryday'
		ORDER BY current_games DESC, elo_s DESC, elo_blitz DESC, elo_rapid DESC, name
		LIMIT 30";
        break;
    case "elo":
        $sql = "SELECT *, candidates_teams.id AS cid
		FROM candidates,candidates_teams
		WHERE candidates.id=candidates_teams.candidates_id
			AND teams_id=$team_id
			AND NOT block
      AND last_invite<'$retryday'
		ORDER BY elo_s DESC, current_games DESC, elo_blitz DESC, elo_rapid DESC, name
		LIMIT 30";
        break;
}

$result = $link->query($sql);
 $x=0;
while ($row = $result->fetch_assoc()) {
 $x++;
 $cid=$row['cid'];
 $username=$row['name'];
 $mf=$row['member_from'];
 $ll=$row['last_login'];
 $li=$row['last_invite'];
 $cg=$row['current_games'];
 $country=$row['country'];
 $location=$row['location'];
 $elos=$row['elo_s'];
 $elo960=$row['elo_960'];
 $elor=$row['elo_rapid'];
 $elob=$row['elo_blitz'];

 echo "<TR>
  <TD align='center'><INPUT type='checkbox' name='blk[]' value=$cid></TD>
  <TD align='center'><INPUT type='checkbox' name='delay[]' value=$cid></TD>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$elor</TD>
  <TD align='center'>$elob</TD>
  <TD align='center'>$country</TD>
  <TD>$location</TD>
  <TD align='center'>$li</TD>

  </TR>";

}
  echo "<INPUT type='hidden' name='invite_type' value='$invite_type'>";
?>

</TABLE>
<TABLE align="left" width="100%">
<TR><TD align='center'><INPUT type='submit'></TD></TR>
</TABLE>


</FORM>

<?php
include "include/navigationend.php";
?>
</body>
</html>
