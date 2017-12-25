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
<TABLE align="left" valign="top" class="mainbody">
<TR><TD valign="top" class="mainbody">	
<h2>Updates</h2>
    <TABLE align="left" class="sortable">
     <TR>
       <TH>Start</TH>
       <TH>End</TH>
	   <TH>Time</TH>
       <TH>Type</TH>       
	   <TH>Players</TH>
	   <TH>Games</TH>
     </TR>

<?php
$sql = "SELECT * FROM update_log,update_type
	WHERE update_log.update_type_id=update_type.id
	ORDER BY update_datetime_start DESC
	LIMIT 200";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $datetimes=$row['update_datetime_start'];
 $datetimee=$row['update_datetime_end'];
 $updatetype=$row['update_type_desc'];
 $num_players=$row['num_players'];
 $num_games=$row['num_games'];
 $date1Timestamp = strtotime($datetimee);
 $date2Timestamp = strtotime($datetimes);
 $timespent=$date1Timestamp-$date2Timestamp;	

echo "<TR>
  <TD>$datetimes</TD>
  <TD>$datetimee</TD>
  <TD align='center'>$timespent</TD>
  <TD>$updatetype</TD>
  <TD align='center'>$num_players</TD>
  <TD align='center'>$num_games</TD>
  </TR>";
}

?>
</TABLE>
</TD></TR>	
<TR><TD valign="top" class="mainbody">	

<h3>JSON Errors</h3>
    <TABLE align="left" class="sortable">
     <TR>
       <TH>Date</TH>
       <TH>Error</TH>
       <TH>URL</TH>
     </TR>

<?php
$sql = "SELECT * FROM api_errors ORDER BY datetime DESC LIMIT 50";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $datetime=$row['datetime'];
 $error=$row['error'];
 $url=$row['url'];
 echo "<TR>
  <TD>$datetime</TD>
  <TD align='center'>$error</TD>
  <TD>$url</TD>
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