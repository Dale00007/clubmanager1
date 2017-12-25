<TABLE class="menu" width="100%" height="100%" border=0 cellspacing=0 cellpadding=0>
	<TR height=110 class="menu"><TD class="menu" height=110 width=250><img src="pics/ChessComCM.gif" width=250 height=100></TD><TD class="menu">

<?php
include "include/topmenu.php";
?>
		</TD><TD width=300 class="menu"><font size=2>
<?php 

if (!empty($_SESSION['username'])){
  echo $_SESSION['username'];
  echo " - (";
  echo $_SESSION['team_id'];
  echo ") ";	
  echo $_SESSION['teamname'];
	if ($_SESSION['admin']=="1") {echo " - admin";}
	if ($_SESSION['mainadmin']=="1") {echo " - siteadmin";}
  }
?>		
		</font></TD></TR>

	<TR class="menu"><TD class="menu" align="center" valign="top" width=200>
		<P align="center"><DIV class="menu">
		<A href="index.php" class="menu">HOME</A><BR><BR>
		<A href="players_list.php" class="menu">PLAYERS</A><BR><BR>
		<A href="matches.php" class="menu">MATCHES</A><BR><BR>
		<A href="recruitment_invite.php?invite_type=elo" class="menu">RECRUITMENT</A><BR><BR>
		<A href="statistics.php" class="menu">STATISTICS</A>
 
<?php
if (!empty($_SESSION['admin'])){
if ($_SESSION['admin']==1) {
  echo "<BR><BR><A href='teamadmin_parms.php' class='menu'>TEAM ADMIN</A>";
  }	
}

if (!empty($_SESSION['mainadmin'])){
if ($_SESSION['mainadmin']==1) {
  echo "<BR><BR><A href='administration.php' class='menu'>ADMIN</A>";
  }	
}
?>		
			
	</DIV></P></TD>
	<TD class="mainbody" colspan=2 valign="top">