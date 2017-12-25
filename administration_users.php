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
	
    
<h2>List of users</h2>
    <div class="container">
    <TABLE align="left">
     <TR>
       <TH>Username</TH>
       <TH>Created</TH>
       <TH>Last login</TH>
       <TH>Mainadmin</TH>
       <TH>Active</TH>
     </TR>

<?php
$sql = "SELECT * FROM users ORDER BY username";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $username=$row['username'];
 $userid=$row['id'];
 $created_at=$row['created_at'];
 $last_login=$row['last_login'];
 $mainadmin=$row['mainadmin'];
 $active=$row['active'];
 echo "<TR>
  <TD>$username</TD>
  <TD>$created_at</TD>
  <TD>$last_login</TD>
  <TD align='center'>$mainadmin</TD>
  <TD align='center'>$active</TD>
  </TR>";
 echo "<TR>
  <TD>Managed teams:</TD>
  <TD colspan=4>";
  	$sqlt = "SELECT teamname, admin 
			FROM users_teams, teams 
			WHERE teams.id=users_teams.teams_id 
			AND users_teams.users_id=$userid
			ORDER BY teamname";
	$resultt = $link->query($sqlt);
	while ($rowt = $resultt->fetch_assoc()) {
		 $teamname=$rowt['teamname'];
		 $admin=$rowt['admin'];
		if ($admin) {echo "<FONT color='gold'>";}
		 echo $teamname."<BR>";
		if ($admin) {echo "</FONT>";}
	}
  
	echo "</TD>
  </TR>";
}

?>

    </TABLE></DIV>

<?php
include "include/navigationend.php";
?>
</body>
</html>
 