<?php
require_once ('src/query.php');
mod_check ();

$queries = array (
		'ALTER TABLE `bandits` ADD `xmas_team_id` INT NOT NULL AFTER  `LyricsWins`',
		'ALTER TABLE `bandits` ADD INDEX `name_index` (`id`,`name` (64))',
		'ALTER TABLE `rounds` ADD start DATETIME NOT NULL',
		'ALTER TABLE `rounds` ADD end DATETIME NOT NULL',
		
	"CREATE TABLE IF NOT EXISTS `votes` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `type` enum('bestSong','bestLyricist','bestMusician','bestVocalist','bestProducer','bestSave','underAppreciatedSong','underAppreciatedBandit') NOT NULL,
	  `banditID` int(11) NOT NULL COMMENT 'The vote caster',
	  `roundID` int(11) NOT NULL,
	  `songID` int(11) NOT NULL,
	  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`),
	  KEY `banditID` (`banditID`,`roundID`),
	  KEY `FK_one_vote_per_round` (`roundID`),
	  KEY `somewhat` (`banditID`,`type`,`roundID`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=180 ;",
			'ALTER TABLE `votes`
	  ADD CONSTRAINT `FK_one_vote_per_round` FOREIGN KEY (`roundID`) REFERENCES `rounds` (`number`) ON DELETE CASCADE ON UPDATE CASCADE,
	  ADD CONSTRAINT `FK_voter` FOREIGN KEY (`banditID`) REFERENCES `bandits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;' ,
	
	"CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subreddit` varchar(255) NOT NULL,
  `template_id` int(16) NOT NULL,
  `messaged_mods` varchar(255) DEFAULT '0',
  `post` varchar(255) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `team_id` int(11) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Used for Xmas - stores the city the team is in' AUTO_INCREMENT=30 ;",

"INSERT INTO `cities` (`id`, `name`, `subreddit`, `template_id`, `messaged_mods`, `post`, `created`, `team_id`, `lat`, `lng`) VALUES
(10, 'Melbourne VIC, Australia', 'melbourne', 1, '1', '0', '0000-00-00 00:00:00', 0, -37.9916645, 145.07893239999999),
(11, 'Sydney NSW, Australia', 'sydney', 1, '1', 'http://www.reddit.com/r/australia/comments/2ei0ua/nbn_co_absolving_their_responsibility_for_25mbps/', '0000-00-00 00:00:00', 0, -33.8674869, 151.20699020000006),
(26, 'Schenectady, NY, USA', '', 0, '0', '0', '2014-09-28 22:25:41', 0, 42.8142432, -73.9395687),
(27, 'Geelong VIC 3220, Australia', '', 0, '0', '0', '2014-09-28 22:33:03', 0, -38.1485437, 144.36134790000006),
(28, 'Drouin East VIC 3818, Australia', '', 0, '0', '0', '2014-09-29 01:31:14', 0, -38.1199559, 145.89111300000002),
(29, 'London, United Kingdom', '', 0, '0', '0', '2014-09-30 13:46:29', 0, 51.5073509, -0.12775829999998223);",
	
	"CREATE TABLE IF NOT EXISTS `xmas_teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `city_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Special team table for storing Xmas teams..' AUTO_INCREMENT=27 ;",

"INSERT INTO `xmas_teams` (`id`, `name`, `city_id`, `created`) VALUES
(1, 'Test Team', 10, '2014-09-09 08:00:01'),
(2, 'Another Test Team Name', 11, '2014-09-09 08:09:19'),
(21, 'Team Name', 24, '2014-09-28 22:15:53'),
(23, 'Schenheads', 26, '2014-09-28 22:25:41'),
(24, 'Tigerz', 27, '2014-09-28 22:33:03'),
(25, 'Druid Team Name', 28, '2014-09-29 01:31:14'),
(26, 'Number 2', 10, '2014-09-29 04:10:08');",
)
;

foreach ( $queries as $sql ) {
	insert_query ( $sql );
}

echo "Tables updated to new schema.";

if (! unlink ( 'update_tables.php' )) {
	echo "<br /><br /><h3>Failed to remove update_tables.php, please delete ASAP!</h3>";
}
?>
