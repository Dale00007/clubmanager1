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
     </TR>

<?php
$sql = "SELECT * FROM teams,search_type
		WHERE search_type.id=teams.search_type_id
		ORDER BY teamname";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $teamname=$row['teamname'];
 $search_type_desc=$row['search_type_desc'];
 $teamlink=$row['teamlink'];
 $p_inactivity_m=$row['p_inactivity_m'];
 $p_invite_retry_m=$row['p_invite_retry_m'];
 $p_country=$row['p_country'];
	
 echo "<TR>
  <TD>$teamname</TD>
  <TD>$teamlink</TD>
  <TD align='center'>$p_invite_retry_m</TD>
  <TD>$search_type_desc</TD>
  <TD>$p_country</TD>

</TR>";

}

?>

    </TABLE></DIV>

<?php
include "include/navigationend.php";
?>
</body>
</html>
 