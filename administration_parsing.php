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
require_once 'include/functions.php';

include "include/header.php";
?>
 

<?php
echo "<body>";
include "include/navigation.php";
?>
	
    
<h2>Testing of parsing and functions</h2>

 
<?php
//updatePlayer("Dale00007");
//updatePlayerStats("dale00007");
//findActivePlayersAll(); 
//updateCandidates();
//updateClubsPlayers();
//updateTeamPlayers("all", $_SESSION['team_id'],"inactive","both");
//findClubCandidates();
findClubsMatches();
?>
	

<?php
include "include/navigationend.php";
?>
</body>
</html>