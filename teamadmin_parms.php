<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if((!isset($_SESSION['username']) OR ($_SESSION['mainadmin']==0)) || empty($_SESSION['username'])){
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


<h2>List of team parameters</h2>
    <div class="container">
    <TABLE align="left">
     <TR>
     <TH>Team</TH>
	   <TH>Teamlink</TH>
     <TH>Invitation retry<br>(months)</TH>
     <TH>Search type</TH>
     <TH>Country</TH>
	   <TH>Location strings</TH>
     <TH>Invite text</TH>
     </TR>

<?php
$sql = "SELECT teams.id as tid, teamlink, teamname, p_inactivity_m, p_invite_retry_m, p_country, p_invitetext,
search_type_id, search_type_desc
FROM teams,search_type
WHERE search_type.id=teams.search_type_id
ORDER BY teamname
";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $teamname=$row['teamname'];
 $search_type_desc=$row['search_type_desc'];
 $search_type_id=$row['search_type_id'];
 $teamlink=$row['teamlink'];
 $p_inactivity_m=$row['p_inactivity_m'];
 $p_invite_retry_m=$row['p_invite_retry_m'];
 $p_country=$row['p_country'];
 $p_invitetext=$row['p_invitetext'];
 $teamid=$row['tid'];

 echo "
 <FORM action='teamadmin_parms.php' method='get'>
  <TR>
  <TD>$teamname</TD>
  <TD><INPUT type='text' name='new_teamlink' value='$teamlink' size=20></TD>
  <TD align='center'><INPUT type='text' name='new_invite_retry_m' value='$p_invite_retry_m' size=2></TD>
  <TD><INPUT type='text' name='new_search_type_id' value='$search_type_id' size=2></TD>
  <TD><INPUT type='text' name='new_country' value='$p_country' size=2></TD>
  <TD>";
if ($search_type_id==2) {
  $sql1 = "SELECT search_text FROM search_template
  		WHERE teams_id=$teamid
  		ORDER BY search_text";
  $result1 = $link->query($sql1);
  while ($row1 = $result1->fetch_assoc()) {
   $stext=$row['search_text'];
   echo "$stext<br>";
 }
} else {
echo " ";
}
echo "</TD>
  <TD><TEXTAREA rows=10 cols=30 name='new_invitetext' value='$p_invitetext'></TEXTAREA></TD>
  <TD><INPUT type='hidden' name='updateteam' value=$teamid>
  <INPUT type='submit' value='UPDATE'></TD>
  </TR>
</FORM>";

}

?>

    </TABLE></DIV>

<?php
include "include/navigationend.php";
?>
</body>
</html>
