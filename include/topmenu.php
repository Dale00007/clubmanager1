<?php
if ($_SERVER['PHP_SELF']=="/index.php") {
	echo " ";}

if (strpos($_SERVER['PHP_SELF'],'players') !== false) {
	echo "<A href='players_list.php' class='menu'>LIST</A> - 
		<A href='players_new.php' class='menu'>NEW</A> - 
		<A href='players_activation.php' class='menu'>ACTIVATION</A> -
		<A href='players_inactive.php' class='menu'>CLOSED/INACTIVE</A> -
	    <A href='players_left.php' class='menu'>LEFT</A>";
		}
		
if (strpos($_SERVER['PHP_SELF'],'administration') !== false) {
	echo "<A href='administration_users.php' class='menu'>USERS</A> - 
		<A href='administration_teams.php' class='menu'>TEAMS</A> -
		<A href='administration_candidates.php' class='menu'>CANDIDATES</A> -
		<A href='administration_parsing.php' class='menu'>PARSING</A> -
		<A href='administration_logs.php' class='menu'>LOGS</A> ";
	}
	
	if (strpos($_SERVER['PHP_SELF'],'teamadmin') !== false) {
	echo "<A href='teamadmin_parms.php' class='menu'>TEAM SETUP</A> - 
		<A href='teamadmin_players.php' class='menu'>PLAYERS SETUP</A>";
	}
?>