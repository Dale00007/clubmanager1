<?php
// Chess.com header
$cctx = array('http'=>array('header'=>'From: pez@atlas.cz'));
$ctx = stream_context_create($cctx);
$headers = array(
    'From: pez@atlas.cz'
);
// List of functions
// updatePlayer(playername) - Update single player
// updatePlayerMatches(playername) - Update single player - Current matches
// updateTeamPlayers(all/team,team_id,active/inactive/both,profile/matches/both) - Update players (all or specific team) - closed players are not updated
// getJson(url) - Nacte json z chess.com
// findActivePlayersAll() - find new active players in all countries
// findActivePlayers($ctry) - find new active players in specific country
// updateCandidate(playername) - Update single player
// updateCandidateMatches(playername) - Update single player - Current matches
// updateCandidates()
// updateClubPlayers(teamlink) - Get list of players in the specific team and update player list (plus identify new and left players)
// updateClubsPlayers() - Get list of players in all teams
// findClubCandidates($teamid,$ctry) - Add players to candidate list
// findClubCandidatesCountry($teamid,$ctry) - Add players to candidate list for teams using country template
// findClubMatches(teamName, teamId, findType) - Find new club matches. FindType can equal - finished, in_progress, registered, all)
// findClubsMatches() - Find new club matches for all teams.
// searchWords(string, array) - Finds if any of strings in array is included in the string
// updateMatchReg (matchId,teamId,teamLink) - Update details of match in registration status
// updateMatch (matchId,teamId,teamLink) - Update details of ongoing or completed match

// Update single player profile
function updatePlayerProfile($playername, $pid, $inactivityday, $typeg) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	echo $playername.'<BR>'.$url.' - httperror: '.$htpperror.'<BR>';
	$obj = json_decode($json);
	$last_online = date('Y-m-d',($obj->last_online));
	$member_from = date('Y-m-d',($obj->joined));
	$location = $obj->location;
	$chess_com_player_id = $obj->player_id;
	$country1 = $obj->country;
	$status = $obj->status;
	$country = substr($country1,34,2);
	$dnes=date("Y-m-d");
  echo "$status<BR>";
	if ($htpperror<>200) {
			if ($htpperror==404) {
	     	$sql="SELECT chess_com_player_id as pid FROM players WHERE username='$playername'";
		     $result = $link->query($sql);
		     $row = $result->fetch_assoc();
	     	$pid=strtolower($row['pid']);
		     $sql="SELECT username as newname FROM players
		         WHERE username<>'$playername'
		         AND chess_com_player_id=$pid";
		     $result = $link->query($sql);
		     $row = $result->fetch_assoc();
	     	$newname=strtolower($row['newname']);
		     if ($newname<>'') {
					     $sql="DELETE FROM players
		         WHERE username='$playername'";
		          $result = $link->query($sql);
		         $sql="UPDATE players
		  		     SET username='$newname'
					     WHERE chess_com_player_id=$pid";
			    }
		    echo $sql.'<BR>';
		$result = $link->query($sql);
		$url = $url." - player renamed";
		$sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		  echo $sql.'<BR>';
		  $result = $link->query($sql);
		} else {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		}
	} else {
			$sql="UPDATE players
		  		SET last_login='$last_online', member_from='$member_from',
					location='$location', country='$country',
					chess_com_player_id=$chess_com_player_id, last_update='$dnes'
					WHERE username='$playername'";
			//echo $sql.'<BR>';
			$result = $link->query($sql);
			$last_online= strtotime($last_online);
			if (strpos($status, "closed") !== false) {
				$sql="UPDATE players_teams
		  				SET players_status_id=3
						  WHERE player_id=$pid";
				$result = $link->query($sql);
        echo $sql;
				} else {
				if ($last_online<=$inactivityday) {
					$sql="UPDATE players_teams
		  				SET players_status_id=2
						  WHERE player_id=$pid";
						echo $sql;
						$result = $link->query($sql);
						} else {
				    	if ($typeg<>"active") {
				     		$sql="UPDATE players_teams
		  			          SET players_status_id=1
					            WHERE player_id=$pid";
					  		echo $sql;
				   			$result = $link->query($sql);
				   			}
      					}
				}
	   }
}
//update single player matches
function updatePlayerMatches($playername) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername/games";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
		if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  	VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		} else {
			$obj = json_decode($json);
			$cg = count($obj->games);
			$sql="UPDATE players
		  		SET current_games=$cg
				WHERE username='$playername'";
			//echo $sql.'<BR>';
			$result = $link->query($sql);
		}
}
function updatePlayerStats($playername) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername/stats";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
		if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  	VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		} else {
			$obj = json_decode($json);
			$chess_daily = $obj->chess_daily;
			$chess_daily_last = $chess_daily->last;
			$chess_daily_record = $chess_daily->record;
			$chess_daily_rating = $chess_daily_last->rating;
			$chess_daily_timeout = $chess_daily_record->timeout_percent;
			$chess_960 = $obj->chess960_daily;
			$chess_960_last = $chess_960->last;
			$chess_960_rating = $chess_960_last->rating;
			$chess_blitz = $obj->chess_blitz;
			$chess_blitz_last = $chess_blitz->last;
			$chess_blitz_rating = $chess_blitz_last->rating;
			$chess_rapid = $obj->chess_rapid;
			$chess_rapid_last = $chess_rapid->last;
			$chess_rapid_rating = $chess_rapid_last->rating;
			$chess_bullet = $obj->chess_bullet;
			$chess_bullet_last = $chess_bullet->last;
			$chess_bullet_rating = $chess_bullet_last->rating;
			$tactics = $obj->tactics;
			$tactics_highest = $tactics->highest;
			$tactics_rating = $tactics_highest->rating;
			$lessons = $obj->lessons;
			$lessons_highest = $lessons->highest;
			$lessons_rating = $lessons_highest->rating;
			$lessons = $obj->lessons;
			$lessons_highest = $lessons->highest;
			$lessons_rating = $lessons_highest->rating;
			$puzzle_rush = $obj->puzzle_rush;
			$puzzle_best = $puzzle_rush->best;
			$puzzle_attempts = $puzzle_best->total_attempts;
			$puzzle_score = $puzzle_best->score;
			if ($chess_daily_rating=='') {$chess_daily_rating=0;}
			if ($chess_rapid_rating=='') {$chess_rapid_rating=0;}
			if ($chess_960_rating=='') {$chess_960_rating=0;}
			if ($chess_blitz_rating=='') {$chess_blitz_rating=0;}
			if ($chess_bullet_rating=='') {$chess_bullet_rating=0;}
			if ($tactics_rating=='') {$tactics_rating=0;}
			if ($lessons_rating=='') {$lessons_rating=0;}
			if ($puzzle_attempts=='') {$puzzle_attempts=0;}
			if ($puzzle_score=='') {$puzzle_score=0;}			
			$sql="UPDATE players
				SET elo_s=$chess_daily_rating, elo_960=$chess_960_rating,
				elo_rapid=$chess_rapid_rating, elo_blitz=$chess_blitz_rating, 
				elo_bullet=$chess_bullet_rating, elo_t=$tactics_rating,
				elo_lessons=$lessons_rating, elo_lessons=$lessons_rating,
				elo_rush_score=$puzzle_score, elo_rush_attempts=$puzzle_attempts,
				to_ratio_site=$chess_daily_timeout
				WHERE username='$playername'";
			echo $sql.'<HR>';
			$result = $link->query($sql);
		}
}
function updateTeamPlayers($type, $team, $inactivity, $typeg) {
	global $link;
	$p_inactivity_m=3;
	$x="-$p_inactivity_m Months";
	$inactivityday=date("y-m-d",strtotime($x));
	$inactivityday= strtotime($inactivityday);
    $dnes=date("Y-m-d");
	if ($type == "all") {
	$sql="SELECT username, players.id AS pid
		  FROM players, players_teams
		  WHERE players.id=players_teams.players_id
		  AND last_update<'$dnes'";
	echo "$sql<br>";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (1)";
	} else {
	$sql="SELECT username, players.id AS pid
		  FROM players_teams,players
		  WHERE players.id=players_teams.players_id
		  AND players_teams.teams_id=$team
		  AND last_update<'$dnes'";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (4)";
	}
	switch ($inactivity) {
    case "active":
        $sql = $sql." AND players_status_id=1";
		$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (2)";
        break;
    case "inactive":
        $sql = $sql." AND players_status_id<>1";
		$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (3)";
        break;
	}
 $sql = $sql." GROUP BY username LIMIT 250";
	echo $sql.'<BR>'.$sqllog.'<BR>';
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
 	//echo "Inserted id: $last_id";
	$result = $link->query($sql);
	$num_rows = mysqli_num_rows($result);
    while ($row = $result->fetch_assoc()) {
  		$username=strtolower($row['username']);
		echo $username."<BR>";
		 $pid=$row['pid'];
			switch ($typeg) {
   			case "profile":
        		updatePlayerProfile($username, $pid, $inactivityday, $typeg);
        		break;
    		case "matches":
        		updatePlayerMatches($username);
				    updatePlayerStats($username);
        		break;
			case "both":
			      updatePlayerProfile($username, $pid, $inactivityday, $typeg);
        		updatePlayerMatches($username);
				    updatePlayerStats($username);
        		break;
			}
		}
	$sqllog="UPDATE update_log
			 SET update_datetime_end=CURRENT_TIMESTAMP, num_players=$num_rows
			 WHERE id=$last_id";
	$result = $link->query($sqllog);
}
function getJson($url) {
	global $headers;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	$result=curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array($result,$httpcode);
}
function findActivePlayers($ctry) {
    global $link;
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (7)";
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
	$sql="SELECT name FROM candidates";
    $result = $link->query($sql);
    $playernames1 = $result->fetch_all(MYSQLI_ASSOC);
	$playernames = array_column($playernames1, 'name');
	//print_r($playernames);
	$url="https://api.chess.com/pub/country/$ctry/players";
	//echo "$url<BR>";
 	$json1=getJson($url);
	$json=$json1[0];
	$htpperror=$json1[1];
	$obj=json_decode($json);
	$oa=(array) $obj;
	$count_new_players=0;
	foreach($oa as $row => $value){
		foreach($value as $row2 => $playername) {
        	echo $playername . " - ";
			if (in_array($playername, $playernames))
  			{
  				echo "Match found";
  			}
			else
  			{
  				echo "Match not found";
				$count_new_players++;
				$sql="INSERT INTO candidates (name)
		  			VALUES('$playername')";
				echo " - $sql";
				$result = $link->query($sql);
  			}
			echo "<br/>";
		}
	}
	$sqllog="UPDATE update_log
			 SET update_datetime_end=CURRENT_TIMESTAMP, num_players=$count_new_players, country='$ctry'
			 WHERE id=$last_id";
	echo $sqllog;
	$result = $link->query($sqllog);
}
function findClubCandidates() {
  global $link;
  $cancnt=0;
  $sqllog="INSERT INTO update_log (update_type_id) VALUES (10)";
  $result = $link->query($sqllog);
  $last_id = $link->insert_id;
  $sql="SELECT id, p_country, search_type_id, teamname
        FROM teams
        WHERE search_type_id=1 OR search_type_id=0";
  $result = $link->query($sql);
  while ($row = $result->fetch_assoc()) {
    $teamid=$row['id'];
    $teamname=$row['teamname'];
    $ctry=$row['p_country'];
    $sti=$row['search_type_id'];
    if ($sti==1) {
      $sql2="SELECT t1.id,name FROM candidates as t1
        LEFT JOIN players as t2 ON t1.name = t2.username
        LEFT JOIN candidates_teams as t3 on t1.id = t3.candidates_id
        WHERE t2.id Is Null
        AND t3.id Is Null
        AND t1.country='$ctry'";
    } else {
      $sql2="SELECT t1.id,name FROM candidates as t1
        LEFT JOIN players as t2 ON t1.name = t2.username
        LEFT JOIN candidates_teams as t3 on t1.id = t3.candidates_id
        WHERE t2.id Is Null
        AND t3.id Is Null
        AND (";
      $sql1="SELECT search_text
            FROM search_template
            WHERE teams_id=$teamid";
      $result1 = $link->query($sql1);
      createSqlLog($sql);
      while ($row1 = $result1->fetch_assoc()) {
        $search_text=$row1['search_text'];
        $sql2=$sql2."(t1.location LIKE '%$search_text%') OR";
        }
        $sql2=substr($sql2, 0, -3);
        $sql2=$sql2.")";
      }
    echo $teamname." - ".$sql2."<HR>";
	$result2 = $link->query($sql2);
	while ($row2 = $result2->fetch_assoc()) {
	    $playerid=$row2['id'];
		$sql3="INSERT INTO candidates_teams (candidates_id,teams_id)
		  	  VALUES($playerid,$teamid)";
                createSqlLog($sql3);
		$result3 = $link->query($sql3);
		$cancnt++;
	    }
  }
  //remove players who joined club already
  $sql="SELECT t1.chess_com_player_id, t2.teams_id, t1.name, t2.id as cid FROM candidates as t1
LEFT JOIN candidates_teams AS t2 ON t2.candidates_id=t1.id
LEFT JOIN players_teams AS t3 ON t2.teams_id=t3.teams_id
LEFT JOIN players AS t4 ON t4.id=t3.players_id
WHERE t1.chess_com_player_id = t4.chess_com_player_id
AND not t3.left_team";
$result = $link->query($sql);
      createSqlLog($sql);
while ($row = $result->fetch_assoc()) {
  $cid=$row['cid'];
  $sqlu="DELETE FROM candidates_teams
    WHERE id=$cid";
  echo "$sqlu<BR>";
        createSqlLog($sqlu);
  $resultu = $link->query($sqlu);
 }
  $sqllog="UPDATE update_log
		SET update_datetime_end=CURRENT_TIMESTAMP, num_players=$cancnt
		WHERE id=$last_id";
  $result = $link->query($sqllog);
}
function findActivePlayersAll() {
  global $link;
  $sql="SELECT code
        FROM candidates_country";
  $result = $link->query($sql);
  while ($row = $result->fetch_assoc()) {
	  $ctry=$row['code'];
	  findActivePlayers($ctry);
	 }
	}
// Update single candidate profile
function updateCandidateProfile($playername, $pid, $inactivityday) {
	global $link;
	$sql="SELECT username FROM players";
    $result = $link->query($sql);
    $playernames1 = $result->fetch_all(MYSQLI_ASSOC);
    $playernames = array_column($playernames1, 'name');
	$url="https://api.chess.com/pub/player/$playername";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	$obj = json_decode($json);
	$last_online = date('Y-m-d',($obj->last_online));
	$member_from = date('Y-m-d',($obj->joined));
	$location = $obj->location;
	$chess_com_player_id = $obj->player_id;
	$country1 = $obj->country;
	$status = $obj->status;
	$country = substr($country1,34,2);
	$dnes=date("Y-m-d");
	if ($htpperror<>200) {
		if ($htpperror==404) {
		$sql="DELETE FROM candidates WHERE name='$playername'";
		echo $sql.'<BR>';
		$result = $link->query($sql);
		$url = $url." - candidate removed";
		$sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		} else {
		$sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		}
	} else {
			if ($status=="closed") {
				$sql="DELETE FROM candidates
					WHERE name='$playername'";
				echo $sql.'<BR>';
        createSqlLog($sql);
				$result = $link->query($sql);
				} else {
				$sql="UPDATE candidates
		  			SET last_login='$last_online', member_from='$member_from',
					location='$location', country='$country',
					chess_com_player_id=$chess_com_player_id, last_check='$dnes'
					WHERE name='$playername'";
				echo $sql.'<BR>';
        createSqlLog($sql);
				$result = $link->query($sql);
				if (in_array($playername, $playernames)) {
					$sql="UPDATE players
		  				SET last_login='$last_online', member_from='$member_from',
							location='$location', country='$country',
							chess_com_player_id=$chess_com_player_id, last_update='$dnes'
						WHERE name='$playername'";
          file_put_contents('./sql_'.date("j.n.Y").'.txt', $sql, FILE_APPEND);
					$result = $link->query($sql);
					}
				$last_online= strtotime($last_online);
				if ($last_online<=$inactivityday) {
					$sql="DELETE FROM candidates
						WHERE name='$playername'";
					echo $sql.'<BR>';
          createSqlLog($sql);
					$result = $link->query($sql);
					}
				}
	}
}
function updateCandidateMatches($playername) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername/games";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
		if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  	VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
		} else {
			$obj = json_decode($json);
			$cg = count($obj->games);
			$sql="UPDATE candidates
		  		SET current_games=$cg
				WHERE name='$playername'";
			echo $sql.'<BR>';
      createSqlLog($sql);
			$result = $link->query($sql);
		}
}
function updateCandidateStats($playername) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername/stats";
	$url = strtolower($url);
  echo $url;
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
		if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  	VALUES('$url',$htpperror)";
			$result = $link->query($sql);
		} else {
			$obj = json_decode($json);
			$chess_daily = $obj->chess_daily;
			$chess_daily_last = $chess_daily->last;
			$chess_daily_rating = $chess_daily_last->rating;
			$chess_960 = $obj->chess960_daily;
			$chess_960_last = $chess_960->last;
			$chess_960_rating = $chess_960_last->rating;
			$chess_blitz = $obj->chess_blitz;
			$chess_blitz_last = $chess_blitz->last;
			$chess_blitz_rating = $chess_blitz_last->rating;
			$chess_rapid = $obj->chess_rapid;
			$chess_rapid_last = $chess_rapid->last;
			$chess_rapid_rating = $chess_rapid_last->rating;
			if ($chess_daily_rating=='') {$chess_daily_rating=0;}
			if ($chess_rapid_rating=='') {$chess_rapid_rating=0;}
			if ($chess_960_rating=='') {$chess_960_rating=0;}
			if ($chess_blitz_rating=='') {$chess_blitz_rating=0;}
			$sql="UPDATE candidates
				SET elo_s=$chess_daily_rating, elo_960=$chess_960_rating,
				elo_rapid=$chess_rapid_rating, elo_blitz=$chess_blitz_rating
				WHERE name='$playername'";
			echo $sql.'<HR>';
			$result = $link->query($sql);
		}
}
function updateCandidates() {
	global $link;
	$p_inactivity_m=1;
	$x="-$p_inactivity_m Months";
	$inactivityday=date("y-m-d",strtotime($x));
	$inactivityday= strtotime($inactivityday);
    $dnes=date("Y-m-d");
	$sql="SELECT name, id
		  FROM candidates
		  ORDER BY last_check ASC
		  LIMIT 300";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (8)";
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
 	//echo "Inserted id: $last_id";
	$resultx = $link->query($sql);
	$num_rows = mysqli_num_rows($resultx);
    while ($rowx = $resultx->fetch_assoc()) {
		$name=strtolower($rowx['name']);
		$id=$rowx['id'];
		updateCandidateProfile($name, $id, $inactivityday);
    updateCandidateMatches($name);
		updateCandidateStats($name);
		}
	$sql2="SELECT t1.id,name FROM candidates_teams as t1
        LEFT JOIN candidates as t2 ON t1.candidates_id = t2.id
        WHERE t2.id Is Null";
        createSqlLog($sql);
	$result2 = $link->query($sql2);
	while ($row2 = $result2->fetch_assoc()) {
		$canid=$row2['id'];
		$sql="DELETE FROM candidates_teams
			  WHERE id=$canid";
        createSqlLog($sql);
		$result = $link->query($sql);
	}
	$sqllog="UPDATE update_log
			 SET update_datetime_end=CURRENT_TIMESTAMP, num_players=$num_rows
			 WHERE id=$last_id";
	$result = $link->query($sqllog);
}
function updateClubPlayers($name,$teamsid) {
	global $found_players;
    global $link;
	$url="https://api.chess.com/pub/club/$name/members";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	$obj = json_decode($json);
	if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
	} else {
  $players_weekly = $obj->weekly;
  $players_monthly = $obj->monthly;
  $players_alltime = $obj->all_time;
  $obj= (object) array_merge((array) $players_weekly, (array) $players_monthly);
  $obj= (object) array_merge((array) $obj, (array) $players_alltime);

	$cp = count($obj);
	$found_players=$found_players+$cp;
	$sql="SELECT username FROM players";
    $result = $link->query($sql);
    $existingPlayers1 = $result->fetch_all(MYSQLI_ASSOC);
    $existingPlayers = array_column($existingPlayers1, 'username');
	$sql="SELECT username
		FROM players,players_teams
		WHERE players_teams.players_id=players.id AND teams_id=$teamsid";
    $result = $link->query($sql);
    $clubPlayers1 = $result->fetch_all(MYSQLI_ASSOC);
    $clubPlayers = array_column($clubPlayers1, 'username');
	$oa1=(array) $obj;
//  echo "<HR>"; print_r($oa1);
  $oa=array_column($oa1,username);
//  echo "<HR>"; print_r($oa); echo "<HR>";

  $fplayers=[];
	foreach($oa as $playername){
    	echo $playername . " - ";
			array_push($fplayers,$playername);
			if (in_array($playername, array_map('strtolower', $clubPlayers)))
  			{
  				echo "$name - $playername - Existing club player";
  			}
			else
  			{
				if (in_array($playername, array_map('strtolower', $existingPlayers)))
  				{
  					echo "$name - $playername - Existing player ADDED to CLUB";
					$sql="SELECT id
		  				FROM players
						WHERE username='$playername'";
					$result = $link->query($sql);
					$row = $result->fetch_assoc();
  					$playerid=$row['id'];
					$sql="INSERT INTO players_teams (players_id,teams_id)
		  				VALUES($playerid,$teamsid)";
					$result = $link->query($sql);
  				}
				else
  				{
  					echo "$name - $playername - NEW PLAYER";
					$sql="INSERT INTO players (username)
		  				VALUES('$playername')";
					$result = $link->query($sql);
					$sql="SELECT id
		  				FROM players
						WHERE username='$playername'";
					echo $sql;
					$result = $link->query($sql);
					$row = $result->fetch_assoc();
  					$playerid=$row['id'];
					$sql="INSERT INTO players_teams (players_id,teams_id)
		  				VALUES($playerid,$teamsid)";
					$result = $link->query($sql);
  				}
  			}
			echo "<br/>";
	}
	print_r($fplayers);
	echo "<BR>";
	print_r($clubPlayers);
	echo "<BR>";
	foreach($clubPlayers as $playername) {
		if (!in_array($playername, array_map('strtolower', $fplayers)))
  				{
  					echo "$name - $playername - Player LEFT the club<BR>";
					$sql="SELECT players_teams.id AS id, left_team
						FROM players,players_teams
						WHERE players_teams.players_id=players.id AND username='$playername' AND teams_id=$teamsid";
					$result = $link->query($sql);
					$row = $result->fetch_assoc();
  					$playerid=$row['id'];
			  		$lt=$row['left_team'];
					if ($lt<>1) {
						$sql="UPDATE players_teams
							SET left_team=1, left_team_date=NOW()
							WHERE id=$playerid";
						echo $sql;
						$result = $link->query($sql);
						}
  				}
		}
	echo "<HR>";
	}
}
function updateClubsPlayers() {
	global $link;
	$sql="SELECT teamlink,id
		  FROM teams
		  ORDER BY id";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (9)";
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
 	//echo "Inserted id: $last_id";
	$result = $link->query($sql);
	$found_players = 0;
	while ($row = $result->fetch_assoc()) {
  		$name=$row['teamlink'];
  		$teamsid=$row['id'];
		updateClubPlayers($name,$teamsid);
		}
	$sqllog="UPDATE update_log
			 SET update_datetime_end=CURRENT_TIMESTAMP, num_players=$found_players
			 WHERE id=$last_id";
	$result = $link->query($sqllog);
}
function findClubMatches($name,$teamsid,$findType) {
  global $found_matches;
  global $link;
  global $foundWord;
	$url="https://api.chess.com/pub/club/$name/matches";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	$obj = json_decode($json);
	if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
	} else {
	$finished = $obj->finished;
  $inprogress = $obj->in_progress;
  $registered = $obj->registered;
  $fm = count($finished);
  $im = count($inprogress);
  $rm = count($registered);
//  echo "$name - finished: $fm - inprogress: $im - registeration: $rm<BR>";
  $sql="SELECT matchid FROM matches WHERE teams_id=$teamsid";
    $result = $link->query($sql);
    $existingClubGames1 = $result->fetch_all(MYSQLI_ASSOC);
    $existingClubGames = array_column($existingClubGames1, 'matchid');
    //print_r($existingClubGames); echo "<BR>";
  $sql="SELECT abbr,id FROM gametype_seasons";
      $result = $link->query($sql);
      $gameSeasons1 = $result->fetch_all(MYSQLI_ASSOC);
      $gameSeasons = array_column($gameSeasons1, 'abbr', 'id');
  if ($findType=="registered") {
    $gameStatus = array ("registered");
  } else {
    $gameStatus = array ("registered", "in_progress", "finished");
  }
  foreach ($gameStatus as $gs) {
   $gameList = $obj->$gs;
   $gl = count($gameList);
   $oa = (array) $gameList;
   foreach ($oa as $row) {
     $row1=(array) $row;
     $matchName = $row1['name'];
     $matchLink = $row1['@id'];
     $matchId = substr($matchLink,32,strlen($matchLink)-31);
     $matchOpponentLink = $row1['opponent'];
     $matchOpponent = substr($matchOpponentLink,31,strlen($matchOpponentLink)-31);
//     echo "$matchId - $matchName - $matchLink - $matchOpponent - $matchOpponentLink";
     //echo "MatchID under process: $matchId<BR>";
     if (in_array($matchId, $existingClubGames))
       {
         //echo "Existing club match<BR>";
       } else {
         //find game status
         switch ($gs) {
           case 'registered':
             $matchStatus=0;
             break;
           case 'in_progress':
             $matchStatus=1;
             break;
           case 'finished':
             $matchStatus=2;
             break;
           }
         //find game type
         $foundWord=searchWords($matchName,$gameSeasons);
         if ($foundWord<>"") {
           $competition=array_search($foundWord,$gameSeasons);
         } else {
           $competition=array_search("Friendly",$gameSeasons);
         }
         $matchName = str_replace("'", "", $matchName);
         $sql="INSERT INTO matches (matchid,teams_id,opponent_link,status,competition,matchname)
             VALUES($matchId,$teamsid,'$matchOpponent',$matchStatus,$competition,'$matchName')";
         $result = $link->query($sql);
         $found_matches++;
         // echo " - New match - ADDED - New count: $found_matches - Found word: $foundWord - Found competition: $competition<BR>";
       }
     }
   }
  return($found_matches);
  }
}
function findClubsMatches() {
	global $link;
  global $found_matches;
	$sql="SELECT teamlink,id
		  FROM teams
		  ORDER BY id";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (11)";
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
	$result = $link->query($sql);
	$found_matches = 0;
	while ($row = $result->fetch_assoc()) {
  		$name=$row['teamlink'];
  		$teamsid=$row['id'];
		  findClubMatches($name,$teamsid,'all');
		}
	$sqllog="UPDATE update_log
			 SET update_datetime_end=CURRENT_TIMESTAMP, num_games=$found_matches
			 WHERE id=$last_id";
	$result = $link->query($sqllog);
}
function searchWords($string,$words) {
    foreach($words as $word)
    {
        if(strpos($string,$word)===0) //spaces either side to force a word
        {
            return $word;
        }
    }
    return false;
}

function updateMatchReg($matchid,$teamid,$teamlink) {
  global $link;
  $url="https://api.chess.com/pub/match/$matchid";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	$obj = json_decode($json);
	if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
	} else {
//  echo "<HR>".$url."<BR>";
  $status = $obj->status;
  $started = date('Y-m-d',($obj->start_time));
  $matchSettings = $obj->settings;
  $rules = $matchSettings->rules;
  $time_class = $matchSettings->time_class;
  $minTeamPlayers = $matchSettings->min_team_players;
  $timeControl = $matchSettings->time_control;
  $teams = $obj->teams;
  $team1 = $teams->team1;
  $team1LinkLong = $team1->{'@id'};
  $team1Link = substr($team1LinkLong,31,strlen($team1LinkLong)-31);
  $team1Name = $team1->name;
  $team2 = $teams->team2;
  $team2LinkLong = $team2->{'@id'};
  $team2Name = $team2->name;
  $team2Link = substr($team2LinkLong,31,strlen($team2LinkLong)-31);
  if ($status=="in_progress") {
    $sql="UPDATE matches
      SET status=1
      WHERE matchid=$matchid
       AND teams_id=$teamid";
    $result = $link->query($sql);
    } else {
    //get players count
    $players1 = $team1->players;
    $players2 = $team2->players;
    $plcnt1=count($players1);
    $plcnt2=count($players2);
    //get match info
    if ($time_class=="daily") {$timeClass="D";} else {$timeClass="L";}
    if ($rules=="chess") {$rules="S";} else {$rules="9";}
    if ($timeControl=="1/259200") {$timeControl=3;} else {$timeControl=0;}
  //get rating average
  for ($i=0;$i<$plcnt1;$i++) {
    if (empty($players1[$i]->rating)){$plrat1[$i]=""; $plcnt1=$plcnt1-1;} else {$plrat1[$i]=$players1[$i]->rating;}
    // echo $plrat1[$i].",";
  }
  for ($i=0;$i<$plcnt2;$i++) {
    if (empty($players2[$i]->rating)){$plrat1[$i]=""; $plcnt2=$plcnt2-1;} else {$plrat2[$i]=$players2[$i]->rating;}
    // echo $plrat2[$i].",";
  }
  rsort($plrat1);rsort($plrat2);
  $currentBoards = min($plcnt1,$plcnt2);
  $sumRat1=0;$sumRat2=0;$basEst1=0;$basEst2=0;$bf50=0;$ba50=0;
  for ($i=0;$i<$currentBoards;$i++) {
    $sumRat1=$sumRat1+$plrat1[$i];
    $sumRat2=$sumRat2+$plrat2[$i];
    if($plrat1[$i]>$plrat2[$i]) {$basEst1++;$basEst1++;}
    if($plrat1[$i]<$plrat2[$i]) {$basEst2++;$basEst2++;}
    if($plrat1[$i]==$plrat2[$i]) {$basEst1++;$basEst2++;}
    $dif=$plrat1[$i]-$plrat2[$i];
    if ($dif>=150) { 	$bf50++;$bf50++;}
    if ($dif>=50 and $dif<150) { 	$bf50=$bf50+1.5;$ba50=$ba50+0.5;}
    if ($dif>=-50 and $dif<50) { 	$bf50=$bf50+1;$ba50=$ba50+1;}
    if ($dif>=-150 and $dif<-50) { 	$bf50=$bf50+0.5;$ba50=$ba50+1.5;}
    if ($dif<-150) { 	$ba50++;$ba50++;}
    //echo "$i -- ".$plrat1[$i].":".$plrat2[$i]." - $basEst1:$basEst2 - $bf50:$ba50 - $sumRat1:$sumRat2<BR>";
  }
  $avg1=round($sumRat1/$currentBoards);
  $avg2=round($sumRat2/$currentBoards);
  if ($team1Link==$teamlink) {
    $homeaway="home";
    $plh=$plcnt1; $pla=$plcnt2;
    $avh=$avg1; $ava=$avg2;
    $beh=$basEst1; $bea=$basEst2;
    $aeh=$bf50; $aea=$ba50;
    $opponentName=$team2Name;
  } else {
    $homeaway="away";
    $plh=$plcnt2; $pla=$plcnt1;
    $avh=$avg2; $ava=$avg1;
    $beh=$basEst2; $bea=$basEst1;
    $aeh=$ba50; $aea=$bf50;
    $opponentName=$team1Name;
  }
    $sql="UPDATE matches
      SET players=$plh,players_o=$pla,
          avgrat=$avh, avgrat_o=$ava,
          basest=$beh, basest_o=$bea,
          advest=$aeh, advest_o=$aea,
          rules='$rules', time_class='$timeClass', started='$started',
          min_team_players=$minTeamPlayers, time_control_day=$timeControl,
          opponent_name='$opponentName'
      WHERE matchid=$matchid
       AND teams_id=$teamid";
       //echo "$sql<BR>";
    $result = $link->query($sql);
   }
  }
}

function createSqlLog($sqlQuery) {
  global $link;
  $type=strtok($sqlQuery,' ');
  $sql="INSERT INTO sql_log (sql_query, type)
      VALUES('$sqlQuery','$type')";
  $result = $link->query($sql);
}

function maintainDatabase() {
  global $link;
  $dateClean=7;
  $x="-$dateClean Days";
  $dateClean=date("y-m-d",strtotime($x));
  $dateClean= strtotime($dateClean);
  $sql="DELETE FROM update_log WHERE update_datetime_start<'$dateClean'";
  echo "$sql<BR>";
  createSqlLog("$sql");
  $result = $link->query($sql);
  $sql="DELETE FROM api_errors WHERE date_time<'$dateClean'";
  echo "$sql<BR>";
  createSqlLog("$sql");
  $result = $link->query($sql);
  $sql="DELETE FROM sql_log WHERE date_time<'$dateClean'";
  createSqlLog("$sql");
  $result = $link->query($sql);
  $sql="OPTIMIZE TABLE `candidates_teams`";
  createSqlLog($sql);
  $result = $link->query($sql);
  $sql="OPTIMIZE TABLE `candidates`";
  createSqlLog($sql);
  $result = $link->query($sql);
  $sql="OPTIMIZE TABLE `update_log`";
  createSqlLog($sql);
  $result = $link->query($sql);
  $sql="OPTIMIZE TABLE `sql_log`";
  createSqlLog($sql);
  $result = $link->query($sql);
  $sql="OPTIMIZE TABLE `api_errors`";
  createSqlLog($sql);
  $result = $link->query($sql);
}

function updateMatch($matchid,$teamid,$teamlink) {
  global $link;
  $url="https://api.chess.com/pub/match/$matchid";
	$url = strtolower($url);
 	$json1=getJson($url);
 	$json=$json1[0];
	$htpperror=$json1[1];
	$obj = json_decode($json);
	if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
	} else {
//  echo "<HR>".$url."<BR>";
  $status = $obj->status;
  $boards = $obj->boards;
  $started = date('Y-m-d',($obj->start_time));
  $ended = date('Y-m-d',($obj->end_time));
  $matchSettings = $obj->settings;
  $rules = $matchSettings->rules;
  $time_class = $matchSettings->time_class;
  $minTeamPlayers = $matchSettings->min_team_players;
  $timeControl = $matchSettings->time_control;

  $teams = $obj->teams;
  $team1 = $teams->team1;
  $team1LinkLong = $team1->{'@id'};
  $team1Link = substr($team1LinkLong,31,strlen($team1LinkLong)-31);
  $team1Name = $team1->name;
  $team1Score = $team1->score;
  $team1Result = $team1->result;

  $team2 = $teams->team2;
  $team2LinkLong = $team2->{'@id'};
  $team2Name = $team2->name;
  $team2Link = substr($team2LinkLong,31,strlen($team2LinkLong)-31);
  $team2Score = $team2->score;
  $team2Result = $team2->result;
  if ($time_class=="daily") {$timeClass="D";} else {$timeClass="L";}
  if ($rules=="chess") {$rules="S";} else {$rules="9";}
  if ($timeControl=="1/259200") {$timeControl=3;} else {$timeControl=0;}

  if ($team1Link==$teamlink) {
    $homeaway="home";
    $opponentName=$team2Name;
    $result=$team1Result;
    $score=$team1Score;
    $scoreOpp=$team2Score;
  } else {
    $homeaway="away";
    $result=$team2Result;
    $score=$team2Score;
    $scoreOpp=$team2Score;
    $opponentName=$team1Name;
  }
    $sql="UPDATE matches
      SET boards=$boards, score=$score, score_opp=$scoreOpp, result='$result',
          rules='$rules', time_class='$timeClass', started='$started', finished='$ended',
          min_team_players=$minTeamPlayers, time_control_day=$timeControl,
          opponent_name='$opponentName'
      WHERE matchid=$matchid
       AND teams_id=$teamid";
       echo "$sql<HR>";
    $result = $link->query($sql);
   }
  }

function updateAllMatches() {
  	global $link;
  	$sql="SELECT matchid,teams_id,teamlink
  		  FROM matches, teams
  		  WHERE matches.teams_id=teams.id AND status<>3";
  	$sqllog="INSERT INTO update_log (update_type_id)
  			VALUES (12)";
  	$result = $link->query($sqllog);
  	$last_id = $link->insert_id;
   	//echo "Inserted id: $last_id";
  	$resultx = $link->query($sql);
  	$num_rows = mysqli_num_rows($resultx);
      while ($rowx = $resultx->fetch_assoc()) {
  		$matchid=$rowx['matchid'];
      $teamsid=$rowx['teams_id'];
      $teamLink=$rowx['teamlink'];
  		updateMatch($matchid, $teamsid, $teamLink);
  		}
      $sqllog="UPDATE update_log
    			 SET update_datetime_end=CURRENT_TIMESTAMP
    			 WHERE id=$last_id";
    	$result = $link->query($sqllog);
    }
?>
