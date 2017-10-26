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
	
    <div class="page-header">
        <h2>Site Administration</h2>
    </div>
 

<?php
include "include/navigationend.php";
?>
</body>
</html>
 