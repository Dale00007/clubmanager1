-- phpMyAdmin SQL Dump
-- version 4.4.4
-- http://www.phpmyadmin.net
--
-- Počítač: sql.endora.cz:3313
-- Vytvořeno: Čtv 26. říj 2017, 07:42
-- Verze serveru: 5.6.28-76.1
-- Verze PHP: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `clubmanager`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `api_errors`
--

CREATE TABLE IF NOT EXISTS `api_errors` (
  `id` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(200) NOT NULL,
  `error` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `candidates`
--

CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `location` varchar(50) NOT NULL,
  `country` varchar(2) NOT NULL,
  `member_from` date NOT NULL,
  `last_login` date NOT NULL,
  `current_games` int(4) NOT NULL,
  `completed_games` int(5) NOT NULL,
  `elo_s` int(4) NOT NULL,
  `elo_960` int(4) NOT NULL,
  `elo_rapid` int(4) NOT NULL,
  `elo_blitz` int(4) NOT NULL,
  `timeout_ratio_site` int(3) NOT NULL,
  `found_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_check` date DEFAULT NULL,
  `chess_com_player_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `candidates_country`
--

CREATE TABLE IF NOT EXISTS `candidates_country` (
  `id` int(11) NOT NULL,
  `code` varchar(2) NOT NULL,
  `namecountry` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `candidates_teams`
--

CREATE TABLE IF NOT EXISTS `candidates_teams` (
  `id` int(11) NOT NULL,
  `candidates_id` int(11) NOT NULL,
  `teams_id` int(11) NOT NULL,
  `last_invite` date NOT NULL,
  `block` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `gametype`
--

CREATE TABLE IF NOT EXISTS `gametype` (
  `id` int(11) NOT NULL,
  `type` varchar(6) NOT NULL,
  `description` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Seznam typů zápasů';

-- --------------------------------------------------------

--
-- Struktura tabulky `gametype_seasons`
--

CREATE TABLE IF NOT EXISTS `gametype_seasons` (
  `id` int(11) NOT NULL,
  `seasons_id` int(11) NOT NULL,
  `gametype_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `matches`
--

CREATE TABLE IF NOT EXISTS `matches` (
  `id` int(8) NOT NULL,
  `matchid` int(8) NOT NULL,
  `started` date NOT NULL,
  `registration_opened` date NOT NULL,
  `finished` date NOT NULL,
  `status` int(2) NOT NULL,
  `boards` int(4) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `opponent_id` int(11) NOT NULL,
  `opponent_name` varchar(30) NOT NULL,
  `score` decimal(4,1) NOT NULL,
  `score_opp` decimal(4,1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `matchstatus`
--

CREATE TABLE IF NOT EXISTS `matchstatus` (
  `id` int(4) NOT NULL,
  `status_desc` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `country` varchar(2) NOT NULL,
  `location` varchar(50) NOT NULL,
  `member_from` date NOT NULL,
  `last_login` date NOT NULL,
  `current_games` int(4) NOT NULL,
  `elo_s` int(4) NOT NULL,
  `elo_960` int(4) NOT NULL,
  `elo_blitz` int(4) NOT NULL,
  `elo_rapid` int(4) NOT NULL,
  `elo_t` int(4) NOT NULL,
  `to_ratio_site` int(3) NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `chess_com_player_id` int(11) NOT NULL,
  `last_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `players_matches`
--

CREATE TABLE IF NOT EXISTS `players_matches` (
  `id` int(11) NOT NULL,
  `matches_id` int(8) NOT NULL,
  `players_teams_id` int(11) NOT NULL,
  `result` decimal(1,1) NOT NULL,
  `timeout` int(1) NOT NULL,
  `timeout_date` date NOT NULL,
  `elo` int(4) NOT NULL,
  `elo_opp` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `players_status`
--

CREATE TABLE IF NOT EXISTS `players_status` (
  `id` int(2) NOT NULL,
  `player_status` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `players_teams`
--

CREATE TABLE IF NOT EXISTS `players_teams` (
  `id` int(11) NOT NULL,
  `players_id` int(4) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `matches_s` int(5) NOT NULL DEFAULT '0',
  `points_s` int(5) NOT NULL DEFAULT '0',
  `active_matches_s` int(4) NOT NULL DEFAULT '0',
  `matches_9` int(5) NOT NULL DEFAULT '0',
  `points_9` int(5) NOT NULL DEFAULT '0',
  `active_matches_9` int(4) NOT NULL DEFAULT '0',
  `timeouts` int(5) NOT NULL DEFAULT '0',
  `last_timeout` date NOT NULL,
  `joined_team` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_team_date` date NOT NULL,
  `block` int(1) NOT NULL DEFAULT '0',
  `left_team` int(1) NOT NULL DEFAULT '0',
  `players_status_id` int(2) NOT NULL DEFAULT '1',
  `parm_max_games` int(3) NOT NULL DEFAULT '100',
  `parm_960` int(1) NOT NULL DEFAULT '0',
  `parm_slow` int(1) NOT NULL DEFAULT '0',
  `parm_comment` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `rating_ranges`
--

CREATE TABLE IF NOT EXISTS `rating_ranges` (
  `id` int(11) NOT NULL,
  `low_elo` int(4) NOT NULL,
  `high_elo` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `score_history`
--

CREATE TABLE IF NOT EXISTS `score_history` (
  `score` decimal(4,1) NOT NULL,
  `score_opp` decimal(4,1) NOT NULL,
  `date` date NOT NULL,
  `matches_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `search_template`
--

CREATE TABLE IF NOT EXISTS `search_template` (
  `id` int(11) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `search_text` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `search_type`
--

CREATE TABLE IF NOT EXISTS `search_type` (
  `id` int(1) NOT NULL,
  `search_type_desc` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `seasons`
--

CREATE TABLE IF NOT EXISTS `seasons` (
  `id` int(11) NOT NULL,
  `season` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Seznam sezon';

-- --------------------------------------------------------

--
-- Struktura tabulky `stat_team_history`
--

CREATE TABLE IF NOT EXISTS `stat_team_history` (
  `id` int(11) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `year` int(4) NOT NULL,
  `players` int(5) NOT NULL,
  `win_s` int(3) NOT NULL,
  `lost_s` int(3) NOT NULL,
  `draw_s` int(3) NOT NULL,
  `matches_s` int(3) NOT NULL,
  `win_960` int(3) NOT NULL,
  `lost_960` int(3) NOT NULL,
  `draw_960` int(3) NOT NULL,
  `matches_960` int(3) NOT NULL,
  `big_match_s_size` int(4) NOT NULL,
  `big_match_960_id` int(8) NOT NULL,
  `big_match_960_size` int(4) NOT NULL,
  `points_for_s` decimal(4,1) NOT NULL,
  `points_against_s` decimal(4,1) NOT NULL,
  `points_for_960` decimal(4,1) NOT NULL,
  `points_against_960` decimal(4,1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `stat_team_structure`
--

CREATE TABLE IF NOT EXISTS `stat_team_structure` (
  `id` int(11) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `active_players` int(4) NOT NULL,
  `pasive_players` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(4) NOT NULL,
  `teamlink` char(30) NOT NULL,
  `teamname` char(30) NOT NULL,
  `p_inactivity_m` smallint(2) NOT NULL DEFAULT '3',
  `p_invite_retry_m` int(2) NOT NULL DEFAULT '3',
  `search_type_id` int(1) NOT NULL DEFAULT '0',
  `p_country` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `update_log`
--

CREATE TABLE IF NOT EXISTS `update_log` (
  `id` int(11) NOT NULL,
  `update_type_id` int(2) NOT NULL,
  `update_datetime_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_datetime_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_players` int(5) NOT NULL DEFAULT '0',
  `num_games` int(5) NOT NULL DEFAULT '0',
  `country` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `update_type`
--

CREATE TABLE IF NOT EXISTS `update_type` (
  `id` int(2) NOT NULL,
  `update_type_desc` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(4) NOT NULL,
  `username` varchar(50) NOT NULL,
  `mainadmin` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `users_teams`
--

CREATE TABLE IF NOT EXISTS `users_teams` (
  `idv` int(6) NOT NULL,
  `users_id` int(4) NOT NULL,
  `teams_id` int(4) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `def` tinyint(1) NOT NULL DEFAULT '0',
  `p_rf_use960` int(1) NOT NULL DEFAULT '1',
  `p_rf_usemaxgames` int(1) NOT NULL DEFAULT '1',
  `p_rf_usepace` int(1) NOT NULL DEFAULT '1',
  `p_rf_removeblocked` int(1) NOT NULL DEFAULT '1',
  `p_rf_mincurrentgames` int(4) NOT NULL DEFAULT '2',
  `p_rf_minlastlogindays` int(3) NOT NULL DEFAULT '10',
  `p_rf_minelo` int(4) NOT NULL DEFAULT '1000',
  `p_rf_minelo960` int(4) NOT NULL DEFAULT '1000',
  `p_rf_maxtoratio` int(3) NOT NULL DEFAULT '10',
  `p_rf_maxtoteam` int(4) NOT NULL DEFAULT '10',
  `p_rf_mincompletedgames` int(4) NOT NULL DEFAULT '10'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `api_errors`
--
ALTER TABLE `api_errors`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `candidates_country`
--
ALTER TABLE `candidates_country`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `candidates_teams`
--
ALTER TABLE `candidates_teams`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `gametype`
--
ALTER TABLE `gametype`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `gametype_seasons`
--
ALTER TABLE `gametype_seasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gametype_seasons_gametype` (`gametype_id`),
  ADD KEY `gametype_seasons_seasons` (`seasons_id`);

--
-- Klíče pro tabulku `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matchstatus_matches` (`status`),
  ADD KEY `teams_matches` (`teams_id`);

--
-- Klíče pro tabulku `matchstatus`
--
ALTER TABLE `matchstatus`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `players_matches`
--
ALTER TABLE `players_matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `players_matches_matches` (`matches_id`),
  ADD KEY `players_matches_players_teams` (`players_teams_id`);

--
-- Klíče pro tabulku `players_status`
--
ALTER TABLE `players_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Klíče pro tabulku `players_teams`
--
ALTER TABLE `players_teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `players_teams_players` (`players_id`),
  ADD KEY `players_teams_players_status` (`players_status_id`),
  ADD KEY `players_teams_teams` (`teams_id`);

--
-- Klíče pro tabulku `rating_ranges`
--
ALTER TABLE `rating_ranges`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `score_history`
--
ALTER TABLE `score_history`
  ADD PRIMARY KEY (`score`),
  ADD KEY `score_history_matches` (`matches_id`);

--
-- Klíče pro tabulku `search_template`
--
ALTER TABLE `search_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `search_template_teams` (`teams_id`);

--
-- Klíče pro tabulku `search_type`
--
ALTER TABLE `search_type`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `stat_team_history`
--
ALTER TABLE `stat_team_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stat_team_history_teams` (`teams_id`);

--
-- Klíče pro tabulku `stat_team_structure`
--
ALTER TABLE `stat_team_structure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stat_team_structure_rating_ranges` (`active_players`),
  ADD KEY `stat_team_structure_teams` (`teams_id`);

--
-- Klíče pro tabulku `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teams_search_type` (`search_type_id`);

--
-- Klíče pro tabulku `update_log`
--
ALTER TABLE `update_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `update_log_update_type` (`update_type_id`);

--
-- Klíče pro tabulku `update_type`
--
ALTER TABLE `update_type`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Klíče pro tabulku `users_teams`
--
ALTER TABLE `users_teams`
  ADD PRIMARY KEY (`idv`),
  ADD KEY `user_teams_teams` (`teams_id`),
  ADD KEY `user_teams_users` (`users_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `api_errors`
--
ALTER TABLE `api_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `candidates_country`
--
ALTER TABLE `candidates_country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `candidates_teams`
--
ALTER TABLE `candidates_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `gametype`
--
ALTER TABLE `gametype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `players_status`
--
ALTER TABLE `players_status`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `players_teams`
--
ALTER TABLE `players_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `search_template`
--
ALTER TABLE `search_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `update_log`
--
ALTER TABLE `update_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `update_type`
--
ALTER TABLE `update_type`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `users_teams`
--
ALTER TABLE `users_teams`
  MODIFY `idv` int(6) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
