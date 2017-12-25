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
	
<h2>List of candidates</h2>
    <div align="left" class="container">
    <TABLE align="left" class="sortable">
     <THEAD>
     <TR>
       <TH>Username</TH>
	   <TH>Current<SPAN class="tooltip">All daily games currently ongoing</SPAN></TH>
	   <TH width=120>Last login<SPAN class="tooltip">Last login to CHess.com</SPAN></TH>
	   <TH width=120>Member<BR>from<SPAN class="tooltip"></SPAN></TH>
	   <TH>Elo<SPAN class="tooltip">Elo for daily chess - standard</SPAN></TH>
	   <TH>E960<SPAN class="tooltip">Elo for daily chess - Chess960</SPAN></TH>
	   <TH>EloB<SPAN class="tooltip">Elo for blitz</SPAN></TH>
	   <TH>EloR<SPAN class="tooltip">Elo for rapid</SPAN></TH>
       <TH>Ctry<SPAN class="tooltip">Country from Chess.com</SPAN></TH>
	   <TH>Location<SPAN class="tooltip">Location from Chess.com</SPAN></TH>
	   <TH width=120>Last check<SPAN class="tooltip">Last update from CHess.com</SPAN></TH>		  
		   </TR>
     </THEAD>
<?php
$team_id=$_SESSION['team_id'];
$sql = "SELECT * 
		FROM candidates 
		ORDER BY name";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $username=$row['name'];
 $mf=$row['member_from'];
 $ll=$row['last_login'];
 $lc=$row['last_check'];
 $cg=$row['current_games'];
 $country=$row['country'];	
 $location=$row['location'];
 $elos=$row['elo_s'];
 $elo960=$row['elo_960'];
 $elo_rapid=$row['elo_rapid'];
 $elo_blitz=$row['elo_blitz'];
	
 echo "<TR>
  <TD><A href='https://www.chess.com/member/$username' target='_blank' class='tablea'>$username</TD>
  <TD align='center'>$cg</TD>
  <TD align='center'>$ll</TD>
  <TD align='center'>$mf</TD>
  <TD align='center'>$elos</TD>
  <TD align='center'>$elo960</TD>
  <TD align='center'>$elo_blitz</TD>
  <TD align='center'>$elo_rapid</TD>
  <TD align='center'>$country</TD>
  <TD>$location</TD>
  <TD align='center'>$lc</TD>
  </TR>";
}
?>
		
<?php
include "include/navigationend.php";
?>
</body>
</html>