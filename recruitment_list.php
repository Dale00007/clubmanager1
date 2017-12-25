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

<?php
$dnes=date ("Y-m-d");

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

<h2>Full list of candidates</h2>

    <FORM action='recruitment_list.php' method='get'>
    <TABLE align="left" width="100%">
    <TR><TD align='center'><INPUT type='submit'></TD></TR>
    </TABLE>
      
    <div align="left" class="container">

    <TABLE align="left" class="sortable" width="100%">

     <THEAD>
     <TR>
     <TH>Block<SPAN class="tooltip">Remove player from candidates</SPAN></TH>
     <TH>Delay<SPAN class="tooltip">Delay invite till the next retry</SPAN></TH>
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
$team_id=$_SESSION['team_id'];
$sql = "SELECT *, candidates_teams.id AS cid
		FROM candidates,candidates_teams
		WHERE candidates.id=candidates_teams.candidates_id
			AND teams_id=$team_id
			AND NOT block
		ORDER BY name";

$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
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
 $cid=$row['cid'];

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
  </TR>
  ";
}
?>

  </TABLE>
  </FORM>
<?php
include "include/navigationend.php";
?>
</body>
</html>
