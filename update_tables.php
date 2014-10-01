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
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Used for Xmas - stores the city the team is in' AUTO_INCREMENT=1 ;"
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
