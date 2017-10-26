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
	if ($htpperror<>200) {
		  $sql="INSERT INTO api_errors (url,error)
		  VALUES('$url',$htpperror)";
		//echo $sql.'<BR>';
		$result = $link->query($sql);
	} else {
			$sql="UPDATE players 
		  		SET last_login='$last_online', member_from='$member_from', 
					location='$location', country='$country',
					chess_com_player_id=$chess_com_player_id, last_update='$dnes' 
					WHERE username='$playername'";
			//echo $sql.'<BR>';
			$result = $link->query($sql);
			$last_online= strtotime($last_online);
			if ($status=="closed") {
				$sql="UPDATE players_teams
		  				SET players_status_id=3
						WHERE players_id=$pid";
				$result = $link->query($sql);
				} else {
				if ($last_online<=$inactivityday) {
					$sql="UPDATE players_teams
		  				SET players_status_id=2
						WHERE players_id=$pid";
						echo $sql;
						$result = $link->query($sql);
						} else {
				    	if ($typeg<>"active") {
				     		$sql="UPDATE players_teams 
		  			          SET players_status_id=1
					          WHERE players_id=$pid";
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
			$cg = count($obj->games)/2;		
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
			$sql="UPDATE players 
				SET elo_s=$chess_daily_rating, elo_9=$chess_960_rating,
				elo_rapid=$chess_rapid_rating, elo_blitz=$chess_blitz_rating
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
	
 $sql = $sql." GROUP BY username LIMIT 100";
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
				//updatePlayerStats($username);
        		break;
			case "both":
			      updatePlayerProfile($username, $pid, $inactivityday, $typeg);
        		updatePlayerMatches($username);
				//updatePlayerStats($username);
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
		$url = $url." - player removed";
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
				$result = $link->query($sql);
				} else {
				$sql="UPDATE candidates 
		  			SET last_login='$last_online', member_from='$member_from', 
					location='$location', country='$country',
					chess_com_player_id=$chess_com_player_id, last_check='$dnes' 
					WHERE name='$playername'";
				echo $sql.'<BR>';
				$result = $link->query($sql);
			
				if (in_array($playername, $playernames)) {
					$sql="UPDATE players 
		  				SET last_login='$last_online', member_from='$member_from', 
							location='$location', country='$country',
							chess_com_player_id=$chess_com_player_id, last_update='$dnes' 
						WHERE name='$playername'";
					$result = $link->query($sql);
					}
				$last_online= strtotime($last_online);
				if ($last_online<=$inactivityday) {
					$sql="DELETE FROM candidates  
						WHERE name='$playername'";
					echo $sql.'<BR>';
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
			$cg = count($obj->games)/2;		
			$sql="UPDATE candidates 
		  		SET current_games=$cg
				WHERE name='$playername'";
			echo $sql.'<BR>';
			$result = $link->query($sql);
		}	
}

function updateCandidateStats($playername) {
	global $link;
	$url="https://api.chess.com/pub/player/$playername/stats";
	$url = strtolower($url);
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
				SET elo_s=$chess_daily_rating, elo_9=$chess_960_rating,
				elo_rapid=$chess_rapid_rating, elo_blitz=$chess_blitz_rating
				WHERE name='$playername'";
			echo $sql.'<HR>';
			$result = $link->query($sql);
		}	
}

function updateCandidates() {
	global $link;
	$p_inactivity_m=3;
	$x="-$p_inactivity_m Months";
	$inactivityday=date("y-m-d",strtotime($x));
	$inactivityday= strtotime($inactivityday);
    $dnes=date("Y-m-d");
	$sql="SELECT name, id
		  FROM candidates
		  ORDER BY last_check ASC
		  LIMIT 200";
	$sqllog="INSERT INTO update_log (update_type_id)
			VALUES (8)";	
	$result = $link->query($sqllog);
	$last_id = $link->insert_id;
 	//echo "Inserted id: $last_id";
	$result = $link->query($sql);
	$num_rows = mysqli_num_rows($result);
    while ($row = $result->fetch_assoc()) {
		$name=strtolower($row['name']);
		$id=$row['id'];
		updateCandidateProfile($name, $id, $inactivityday);
        updateCandidateMatches($name);
		//updateCandidateStats($name);
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
		
	$cp = count($obj->players);	
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

	$oa=(array) $obj;
	$fplayers=[];	
	foreach($oa as $row => $value){
		foreach($value as $row2 => $playername) {
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

?>