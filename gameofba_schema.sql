-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 12, 2014 at 01:37 PM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gameofba_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `bandits`
--

CREATE TABLE IF NOT EXISTS `bandits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE latin1_general_ci NOT NULL,
  `is_mod` tinyint(1) NOT NULL,
  `banned` tinyint(1) NOT NULL,
  `website` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `tools` text COLLATE latin1_general_ci NOT NULL,
  `soundcloud_url` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `TeamWins` int(11) DEFAULT NULL,
  `MusicWins` int(11) DEFAULT NULL,
  `VocalsWins` int(11) DEFAULT NULL,
  `LyricsWins` int(11) DEFAULT NULL,
  `xmas_team_id` int(11) NOT NULL,
  `xmas_team_status` enum('pending','approved','banned') COLLATE latin1_general_ci NOT NULL COMMENT '''Bandits must be approved by the team creator before they are "Officially" in the team.''',
  `real_name` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `xmas_tc` timestamp NULL DEFAULT NULL COMMENT 'NULL if they haven''t agreed, the timestamp of WHEN they agreed if they have.',
  `xmas_share` float DEFAULT NULL COMMENT 'share of revenue allocated by team creator\n',
  `xmas_purchased` timestamp NULL DEFAULT NULL COMMENT 'when the collection was purchased (note, transaction completed, not initiated)',
  `xmas_paid` float DEFAULT NULL COMMENT 'what the bandit paid for the collection',
  `xmas_share_change` tinyint(1) DEFAULT '0' COMMENT 'Has bandit agreed to new change to share allocations?\n',
  PRIMARY KEY (`id`),
  KEY `name_index` (`id`,`name`(64)),
  KEY `FK_team_idx` (`xmas_team_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=277 ;

-- --------------------------------------------------------

--
-- Table structure for table `bestof2013`
--

CREATE TABLE IF NOT EXISTS `bestof2013` (
  `bandit` text COLLATE latin1_general_ci NOT NULL,
  `bestSong` int(11) DEFAULT NULL,
  `bestLyricist` int(11) DEFAULT NULL,
  `bestMusician` int(11) DEFAULT NULL,
  `bestVocalist` int(11) DEFAULT NULL,
  `bestSave` text COLLATE latin1_general_ci,
  `underAppreciatedSong` int(11) DEFAULT NULL,
  `underAppreciatedBandit` text COLLATE latin1_general_ci,
  `bestApplicationRound` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `charities`
--

CREATE TABLE IF NOT EXISTS `charities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of charity',
  `locality` varchar(255) NOT NULL COMMENT 'Where the charity is located, Address/City/Country',
  `email` varchar(255) NOT NULL COMMENT 'Best contact address for charity, email, website ',
  `charity_id` varchar(255) NOT NULL COMMENT 'This charities unique identifier for the locality, All legal charities must be registered by the government of the country they are based in, they are issued an ID',
  `status` enum('approved','nominated','paid','refused') NOT NULL COMMENT 'State of this charity, refused means they didn''t want to be a part of this.',
  `mod_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `FK_mod_idx` (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Store information about the Charities who are nominated, and ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subreddit` varchar(255) NOT NULL,
  `post_template_id` int(16) NOT NULL,
  `message_template_id` int(11) NOT NULL,
  `messaged_mods` varchar(255) DEFAULT '0',
  `post` varchar(255) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `spatial` (`id`,`name`,`lat`,`lng`),
  KEY `fk_cities_1_idx` (`post_template_id`),
  KEY `fk_cities_2_idx` (`message_template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Used for Xmas - stores the city the team is in' AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `finalBestof2013`
--

CREATE TABLE IF NOT EXISTS `finalBestof2013` (
  `bandit` text COLLATE latin1_general_ci NOT NULL,
  `bestSong` int(11) DEFAULT NULL,
  `bestLyricist` int(11) DEFAULT NULL,
  `bestMusician` int(11) DEFAULT NULL,
  `bestVocalist` int(11) DEFAULT NULL,
  `bestSave` text COLLATE latin1_general_ci,
  `underAppreciatedSong` int(11) DEFAULT NULL,
  `underAppreciatedBandit` text COLLATE latin1_general_ci,
  `bestApplicationRound` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flair`
--

CREATE TABLE IF NOT EXISTS `flair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE latin1_general_ci NOT NULL,
  `css` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=86 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `user_to` text COLLATE latin1_general_ci NOT NULL,
  `user_from` text COLLATE latin1_general_ci NOT NULL,
  `body` text COLLATE latin1_general_ci NOT NULL,
  `date_sent` date NOT NULL,
  `new` tinyint(1) NOT NULL DEFAULT '1',
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `nominations`
--

CREATE TABLE IF NOT EXISTS `nominations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `bandit_id` int(11) NOT NULL COMMENT 'FK bandits.id',
  `charity_id` int(11) NOT NULL COMMENT 'FK charities.id',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `I_bid` (`bandit_id`),
  KEY `I_cid` (`charity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Maintain a record of each nomination for a charity, normalised' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE IF NOT EXISTS `rounds` (
  `number` int(3) NOT NULL,
  `theme` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `signupID` text COLLATE latin1_general_ci NOT NULL,
  `musiciansSignupID` text COLLATE latin1_general_ci NOT NULL,
  `lyricistsSignupID` text COLLATE latin1_general_ci NOT NULL,
  `vocalistSignupID` text COLLATE latin1_general_ci NOT NULL,
  `consolidationID` text COLLATE latin1_general_ci NOT NULL,
  `themeID` text COLLATE latin1_general_ci NOT NULL,
  `songvotingthreadID` text COLLATE latin1_general_ci NOT NULL,
  `congratsID` text COLLATE latin1_general_ci NOT NULL,
  `announceID` text COLLATE latin1_general_ci NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sent_messages`
--

CREATE TABLE IF NOT EXISTS `sent_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier for this message',
  `type` enum('charity','city_post','city_message','bandit_pm','bandit_email') NOT NULL COMMENT 'What type of recipient is this? (informs table selection for id',
  `recipient` varchar(255) NOT NULL COMMENT 'who did we contact?\nIf we contacted a subreddit, we''ll save the thread_id here\nIf we contacted a reddit-user, we''ll save the message_id here\nif we contacted someone via email, well save their address here.\n',
  `subject` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'specify exact timestamp of communication',
  `recipient_id` int(11) NOT NULL COMMENT 'from recipients table, ie: team_id, or city_id, or charity_id etc.\nCan''t use FK constraints',
  `mod_id` int(11) NOT NULL COMMENT 'ID of the person initiating contact',
  `text` text NOT NULL COMMENT 'body of message, NOTE: that which was sent, not macros, after lexical parsing/etc.',
  `ref` varchar(11) NOT NULL COMMENT 'reddit messages have a distinct reference identifier.',
  PRIMARY KEY (`id`),
  KEY `I_MOD` (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Maintain an archive of all messages sent, to whom, when, what we said etc.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE IF NOT EXISTS `songs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE latin1_general_ci NOT NULL,
  `url` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `music` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `lyrics` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `vocals` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `lyricsheet` text COLLATE latin1_general_ci,
  `round` int(3) NOT NULL,
  `votes` int(5) DEFAULT NULL,
  `winner` tinyint(1) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `musicvote` int(11) DEFAULT NULL,
  `lyricsvote` int(11) DEFAULT NULL,
  `vocalsvote` int(11) DEFAULT NULL,
  `teamnumber` int(11) NOT NULL,
  `submitby` text COLLATE latin1_general_ci NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `posted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=314 ;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round` int(11) NOT NULL,
  `teamnumber` int(11) NOT NULL,
  `musician` text COLLATE latin1_general_ci NOT NULL,
  `lyricist` text COLLATE latin1_general_ci NOT NULL,
  `vocalist` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('bestSong','bestLyricist','bestMusician','bestVocalist','bestProducer','bestSave','underAppreciatedSong','underAppreciatedBandit','bestXmasSong') NOT NULL,
  `banditID` int(11) NOT NULL COMMENT 'The vote caster',
  `roundID` int(11) NOT NULL,
  `songID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_one_vote_per_round` (`roundID`),
  KEY `banditID` (`banditID`,`roundID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xmas_teams`
--

CREATE TABLE IF NOT EXISTS `xmas_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` enum('new','attention','ready','submitted','approved') DEFAULT 'new' COMMENT 'Define specific states for the team to exist in.\n',
  `city_id` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nominated_charity` int(11) DEFAULT NULL COMMENT 'ID number of the charity.',
  `song_name` varchar(255) DEFAULT NULL COMMENT 'I imagine the song won''t be called the team-name.\n',
  `filename` varchar(255) DEFAULT NULL COMMENT 'save the uploaded file into something meaningful.',
  `song_url` varchar(255) DEFAULT NULL COMMENT 'If we do decide to upload the songs via the SoundCloud API, we''ll need a place to record the URL they give us.',
  `lyrics` text,
  PRIMARY KEY (`id`),
  KEY `creator` (`creator`),
  KEY `city_id` (`city_id`),
  KEY `fk_xmas_teams_2_idx` (`nominated_charity`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Special team table for storing Xmas teams..' AUTO_INCREMENT=27 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `charities`
--
ALTER TABLE `charities`
  ADD CONSTRAINT `FK_mod` FOREIGN KEY (`mod_id`) REFERENCES `bandits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nominations`
--
ALTER TABLE `nominations`
  ADD CONSTRAINT `FK_bid` FOREIGN KEY (`bandit_id`) REFERENCES `bandits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_cid` FOREIGN KEY (`charity_id`) REFERENCES `charities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sent_messages`
--
ALTER TABLE `sent_messages`
  ADD CONSTRAINT `sent_messages_ibfk_1` FOREIGN KEY (`mod_id`) REFERENCES `bandits` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `FK_one_vote_per_round` FOREIGN KEY (`roundID`) REFERENCES `rounds` (`number`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_voter` FOREIGN KEY (`banditID`) REFERENCES `bandits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `xmas_teams`
--
ALTER TABLE `xmas_teams`
  ADD CONSTRAINT `fk_xmas_teams_1` FOREIGN KEY (`creator`) REFERENCES `bandits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_xmas_teams_2` FOREIGN KEY (`nominated_charity`) REFERENCES `charities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
