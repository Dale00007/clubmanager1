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

if (strpos($_SERVER['PHP_SELF'],'recruitment') !== false) {
	echo "<A href='recruitment_list.php' class='menu'>FULL LIST</A> -
		INVITES (<A href='recruitment_invite.php?invite_type=current_games' class='menu'>GAMES</A>,
		<A href='recruitment_invite.php?invite_type=elo' class='menu'>ELO</A>)";
		}

if (strpos($_SERVER['PHP_SELF'],'administration') !== false) {
	echo "<A href='administration_users.php' class='menu'>USERS</A> -
		<A href='administration_teams.php' class='menu'>TEAMS</A> -
		<A href='administration_candidates.php' class='menu'>CANDIDATES</A> -
		<A href='administration_parsing.php' class='menu'>PARSING</A> -
		<A href='administration_logs.php' class='menu'>LOGS</A> -
		<A href='administration_sqllog.php' class='menu'>SQL</A> ";
	}

	if (strpos($_SERVER['PHP_SELF'],'teamadmin') !== false) {
	echo "<A href='teamadmin_parms.php' class='menu'>TEAM SETUP</A> -
		<A href='teamadmin_players.php' class='menu'>PLAYERS SETUP</A>";
	}

	if (strpos($_SERVER['PHP_SELF'],'matches') !== false) {
	echo "<A href='matches_list.php' class='menu'>TEAM MATCHES</A> -
		<A href='matches_registration.php' class='menu'>REGISTRATION</A> -
		<A href='matches_completed.php' class='menu'>COMPLETED</A> - ";
	}
?>
