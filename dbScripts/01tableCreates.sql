CREATE TABLE `deanery`
(
    `id`          int unsigned                           NOT NULL AUTO_INCREMENT,
    `deaneryName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `northSouth`  enum ('north', 'south', 'outOfCounty', 'n/a'),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ringer`
(
    `id`        int unsigned                           NOT NULL AUTO_INCREMENT,
    `firstName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `lastName`  varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `notes`     text COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `judge`
(
    `id`        int unsigned                           NOT NULL AUTO_INCREMENT,
    `firstName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `lastName`  varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ringerID`  int unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_judge_ringer_id_idx` (`ringerID`),
    CONSTRAINT `fk_judge_ringer_id` FOREIGN KEY (`ringerID`) REFERENCES `ringer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `location`
(
    `id`          int unsigned                           NOT NULL AUTO_INCREMENT,
    `location`    varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deaneryID`   int unsigned                           NOT NULL,
    `dedication`  varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tenorWeight` varchar(7) COLLATE utf8mb4_unicode_ci  NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_deanery_location_idx` (`deaneryID`),
    CONSTRAINT `fk_deanery_location` FOREIGN KEY (`deaneryID`) REFERENCES `deanery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `team`
(
    `id`        int unsigned                            NOT NULL AUTO_INCREMENT,
    `teamName`  varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deaneryID` int unsigned                            NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_deanery_team_idx` (`deaneryID`),
    CONSTRAINT `fk_deanery_team` FOREIGN KEY (`deaneryID`) REFERENCES `deanery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='first year is got from results and event links';

CREATE TABLE `DRL_competition`
(
    `id`              int unsigned                           NOT NULL AUTO_INCREMENT,
    `competitionName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `isSingleTower`   tinyint default 0                      not null,
    `usualLocationID` int unsigned,
    PRIMARY KEY (`id`),
    KEY `fk_DRL_competition_location` (`usualLocationID`),
    CONSTRAINT `fk_DRL_competition` FOREIGN KEY ('usualLocationID') REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `DRL_event`
(
    `id`             int unsigned      NOT NULL AUTO_INCREMENT,
    `year`           year(4)           NOT NULL,
    `competitionID`  int unsigned      NOT NULL,
    `locationID`     int unsigned      NOT NULL,
    `isUnusualTower` tinyint default 0 not null,
    PRIMARY KEY (`id`),
    KEY `fk_competition_event_idx` (`competitionID`),
    KEY `fk_location_event_idx` (`locationID`),
    CONSTRAINT `fk_competition_event` FOREIGN KEY (`competitionID`) REFERENCES `DRL_competition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_location_event` FOREIGN KEY (`locationID`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `DRL_event_judge`
(
    `id`      int unsigned NOT NULL AUTO_INCREMENT,
    `eventID` int unsigned NOT NULL,
    `judgeID` int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_DRL_event_DRL_event_judge_idx` (`eventID`),
    KEY `fk_judge_DRL_event_judge_idx` (`judgeID`),
    CONSTRAINT `fk_DRL_event_DRL_event_judge` FOREIGN KEY (`eventID`) REFERENCES `DRL_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_judge_DRL_event_judge` FOREIGN KEY (`judgeID`) REFERENCES `judge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `DRL_incomplete_competition`
(
    `id`      int unsigned NOT NULL AUTO_INCREMENT,
    `eventID` int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_event_incompleteCompetition_idx` (`eventID`),
    CONSTRAINT `fk_event_incompleteCompetition` FOREIGN KEY (`eventID`) REFERENCES `DRL_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `DRL_result`
(
    `id`         int unsigned     NOT NULL AUTO_INCREMENT,
    `position`   tinyint unsigned NOT NULL,
    `pealNumber` tinyint unsigned DEFAULT NULL,
    `faults`     float            NOT NULL,
    `teamID`     int unsigned     NOT NULL,
    `eventID`    int unsigned     NOT NULL,
    `points`     tinyint unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_DRL_result_team_idx` (`teamID`),
    KEY `fk_DRL_result_event_idx` (`eventID`),
    CONSTRAINT `fk_DRL_result_event` FOREIGN KEY (`eventID`) REFERENCES `DRL_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_DRL_result_team` FOREIGN KEY (`teamID`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `DRL_event_winning_ringer`
(
    `id`       int unsigned                          NOT NULL AUTO_INCREMENT,
    `bell`     varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ringerID` int unsigned                          NOT NULL,
    `eventID`  int unsigned                          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_DRL_event_winner_ringer_idx` (`ringerID`),
    KEY `fk_DRL_event_winner_event_idx` (`eventID`),
    CONSTRAINT `fk_DRL_event_winner_event` FOREIGN KEY (`eventID`) REFERENCES `DRL_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_DRL_event_winner_ringer` FOREIGN KEY (`ringerID`) REFERENCES `ringer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `extended_ring`
(
    `id`               int unsigned NOT NULL AUTO_INCREMENT,
    `locationID`       int unsigned NOT NULL,
    `date`             date         NOT NULL,
    `footnote`         text,
    `extendedRingName` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_extended_ring_location_idx` (`locationID`),
    CONSTRAINT `fk_extended_ring_location` FOREIGN KEY (`locationID`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `extended_ring_part`
(
    `id`              int unsigned     NOT NULL AUTO_INCREMENT,
    `extendedRingId`  int unsigned     NOT NULL,
    `numberOfChanges` int unsigned     NOT NULL,
    `partNumber`      tinyint unsigned NOT NULL,
    `time`            time             NOT NULL,
    `partName`        varchar(225) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_extended_ring_part_extended_ring_idx` (`extendedRingId`),
    CONSTRAINT `fk_extended_ring_part_extended_ring` FOREIGN KEY (`extendedRingId`) REFERENCES `extended_ring` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `extended_ring_part_ringer`
(
    `id`                 int unsigned                           NOT NULL AUTO_INCREMENT,
    `extendedRingPartID` int unsigned                           NOT NULL,
    `ringerId`           int unsigned                           NOT NULL,
    `bell`               varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `isConductor`        tinyint unsigned                       NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_extended_ring_part_ringer_part_idx` (`extendedRingPartID`),
    KEY `fk_extended_ring_part_ringer_ringer_idx` (`ringerId`),
    CONSTRAINT `fk_extended_ring_part_ringer_part` FOREIGN KEY (`extendedRingPartID`) REFERENCES `extended_ring_part` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_extended_ring_part_ringer_ringer` FOREIGN KEY (`ringerId`) REFERENCES `ringer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `extended_ring_judge`
(
    `id`             int unsigned NOT NULL AUTO_INCREMENT,
    `extendedRingID` int unsigned NOT NULL,
    `judgeID`        int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_extended_ring_judge_ring_idx` (`extendedRingID`),
    KEY `fk_extended_ring_judge_judge_idx` (`judgeID`),
    CONSTRAINT `fk_extended_ring_judge_judge` FOREIGN KEY (`judgeID`) REFERENCES `judge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_extended_ring_judge_ring` FOREIGN KEY (`extendedRingID`) REFERENCES `extended_ring` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ladder_season`
(
    `id`          int unsigned                       NOT NULL AUTO_INCREMENT,
    `description` text COLLATE utf8mb4_unicode_ci    NOT NULL,
    `yearCode`    char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
    `year`        year                               NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ladder_section`
(
    `id`          int unsigned                            NOT NULL AUTO_INCREMENT,
    `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `sectionCode` char(1) COLLATE utf8mb4_unicode_ci      NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ladder_fixture`
(
    `id`          int unsigned                          NOT NULL AUTO_INCREMENT,
    `locationID`  int unsigned                          NOT NULL,
    `seasonID`    int unsigned                          NOT NULL,
    `sectionID`   int unsigned                          NOT NULL,
    `ladderGroup` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_ladder_fixture_location_idx` (`locationID`),
    KEY `fk_ladder_fixture_season_idx` (`seasonID`),
    KEY `fk_ladder_fixture_section_idx` (`sectionID`),
    CONSTRAINT `fk_ladder_fixture_location` FOREIGN KEY (`locationID`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ladder_fixture_season` FOREIGN KEY (`seasonID`) REFERENCES `ladder_season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ladder_fixture_section` FOREIGN KEY (`sectionID`) REFERENCES `ladder_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ladder_fixture_judge`
(
    `id`        int unsigned NOT NULL AUTO_INCREMENT,
    `fixtureID` int unsigned NOT NULL,
    `judgeID`   int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_ladder_fixture_judge_fixture_idx` (`fixtureID`),
    KEY `fk_ladder_fixture_judge_judge_idx` (`judgeID`),
    CONSTRAINT `fk_ladder_fixture_judge_fixture` FOREIGN KEY (`fixtureID`) REFERENCES `ladder_fixture` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ladder_fixture_judge_judge` FOREIGN KEY (`judgeID`) REFERENCES `judge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ladder_result`
(
    `id`          int unsigned       NOT NULL AUTO_INCREMENT,
    `fixtureId`   int unsigned       NOT NULL,
    `teamId`      int unsigned       NOT NULL,
    `faults`      float              NOT NULL,
    `winLoseDraw` enum ('w','l','d') NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_ladder_result_fixture_idx` (`fixtureId`),
    KEY `fk_ladder_result_team_idx` (`teamId`),
    CONSTRAINT `fk_ladder_result_fixture` FOREIGN KEY (`fixtureId`) REFERENCES `ladder_fixture` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ladder_result_team` FOREIGN KEY (`teamId`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `other_competition`
(
    `id`              int unsigned                            NOT NULL AUTO_INCREMENT,
    `competitionName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `isSingleTower`   tinyint default 0                       not null,
    `usualLocationID` int unsigned,
    PRIMARY KEY (`id`),
    KEY `fk_DRL_competition_location` (`usualLocationID`),
    CONSTRAINT `fk_DRL_competition` FOREIGN KEY ('usualLocationID') REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `other_event`
(
    `id`             int unsigned      NOT NULL AUTO_INCREMENT,
    `year`           year              NOT NULL,
    `competitionID`  int unsigned      NOT NULL,
    `locationID`     int unsigned      NOT NULL,
    `isUnusualTower` tinyint default 0 not null,
    PRIMARY KEY (`id`),
    KEY `fk_other_event_competition_idx` (`competitionID`),
    KEY `fk_other_event_location_idx` (`locationID`),
    CONSTRAINT `fk_other_event_competition` FOREIGN KEY (`competitionID`) REFERENCES `other_competition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_other_event_location` FOREIGN KEY (`locationID`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `other_result`
(
    `id`         int unsigned     NOT NULL AUTO_INCREMENT,
    `position`   tinyint unsigned NOT NULL,
    `pealNumber` tinyint unsigned DEFAULT NULL,
    `faults`     float            NOT NULL,
    `teamID`     int unsigned     NOT NULL,
    `eventID`    int unsigned     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_other_result_team_idx` (`teamID`),
    KEY `fk_other_result_event_idx` (`eventID`),
    CONSTRAINT `fk_other_result_event` FOREIGN KEY (`eventID`) REFERENCES `other_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_other_result_team` FOREIGN KEY (`teamID`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
