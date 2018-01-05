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
<h2>SQL Logged Events</h2>
    <TABLE align="left" class="sortable">
     <TR>
       <TH width="150">Datetime</TH>
       <TH width="80">Type</TH>
	     <TH>Query</TH>
     </TR>

<?php
$sql = "SELECT * FROM sql_log
	ORDER BY datelog DESC
	LIMIT 5000";
$result = $link->query($sql);
while ($row = $result->fetch_assoc()) {
 $dateLog=$row['datelog'];
 $type=$row['type'];
 $sqlQuery=$row['sql_query'];

echo "<TR>
  <TD>$dateLog</TD>
  <TD>$type</TD>
  <TD>$sqlQuery</TD>
  </TR>";
}

?>
</TABLE>

<?php
include "include/navigationend.php";
?>
</body>
</html>
