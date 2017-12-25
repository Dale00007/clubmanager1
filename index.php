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

// Change default team
if (!empty($_GET['team'])){$team = $_GET['team'];} 
if (!empty($_GET['usrt'])){$usrt = $_GET['usrt'];
	$sql = "UPDATE users_teams SET def=0 WHERE users_id=$usrt";
  	$result = $link->query($sql);
	$sql = "UPDATE users_teams SET def=1 WHERE idv=$team";
  	$result = $link->query($sql);
    $sql = "SELECT teamname,teams_id FROM users,teams,users_teams WHERE idv=$team AND users.id=users_teams.users_id AND teams.id=users_teams.teams_id and users_teams.def=1";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
	$teamname=$row['teamname']; 
	$_SESSION['teamname']=$teamname;	
	$teamid=$row['teams_id']; 
	$_SESSION['team_id']=$teamid;
	} 

include "include/header.php";
?>
	
<?php
echo "<body>";
include "include/navigation.php";
?>
	

<h2>Hi, <b><?php echo $_SESSION['username']; ?></b>.<BR>Welcome to Chess.com Club Manager.</h2>
<h3>You are now managing: 

<?php
  $user=$_SESSION['username'];
  $sql = "SELECT teamname FROM users,teams,users_teams WHERE users.username='$user' AND users.id=users_teams.users_id AND teams.id=users_teams.teams_id and users_teams.def=1";
  $result = $link->query($sql);
  $row = $result->fetch_assoc();
		$teamname=$row['teamname'];        
  echo $teamname;
?>
</h3>
<h3>Change team: 

<?php
echo "<form action='index.php' method='get'><select name='team'>";
  $sqlt = "SELECT teamname,idv,def,users.id AS uid FROM users,teams,users_teams WHERE users.username='$user' AND users.id=users_teams.users_id AND teams.id=users_teams.teams_id";
  
	echo $sqlt;
  $rest = $link->query($sqlt);
	while($rowt = $rest->fetch_assoc()) {
        $tn=$rowt['teamname'];
        $ta=$rowt['idv'];
		$td=$rowt['def'];
		$uid=$rowt['uid'];
        echo "<option value='$ta'";
        if ($td==1) {echo " selected"; }
        echo ">$tn</option>";         
		};
        echo "</select>
   <INPUT type='hidden' name='usrt' value='$uid'>	
   <INPUT type='submit' value='CHANGE'></FORM>
 </FORM>";
?>
	   			
		</h3>	
    <p><a href="logout.php">Sign Out of Your Account</a></p>
<TABLE align="left"><TR><TD>
<h2>New players in your club in last 7 days</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH width=120>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=160>Joined club<SPAN class="tooltip">When player was found as club member</SPAN></TH>	 
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
			AND NOT left_team
			AND players_status_id=1
			AND joined_team > NOW() - INTERVAL 7 DAY 
		ORDER BY joined_team DESC
		LIMIT 10";
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
 $m=$m9+$ms;
	$ag=$ag9+$ags;
	$p=$p9+$ps;
	
 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$jt</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$country</TD>
  <TD>$location</TD>
  </TR>";
}
?>
</TABLE>	
</TD></TR>
<TR><TD>	
<h2>Players who left your club in last 7 days</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH width=120>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=120>Left team<SPAN class="tooltip">When player was found as club member</SPAN></TH>	 
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
			AND players_status_id=1
			AND left_team 
		ORDER BY left_team_date DESC
		LIMIT 10";
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
 $lt=$row['left_team_date'];	
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
 $m=$m9+$ms;
	$ag=$ag9+$ags;
	$p=$p9+$ps;
	
 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$lt</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$country</TD>
  <TD>$location</TD>
  </TR>";
}
?>
</TD></TR>		 
</TABLE>
</TABLE>
		
<?php
include "include/navigationend.php";
?>
</body>
</html>
 