USE `ringingV2`;
DROP function IF EXISTS `f_event_numberOfTeams`;

DELIMITER $$
USE `ringingV2`$$
CREATE FUNCTION `f_event_numberOfTeams` (
    p_eventId INT
    )
    RETURNS INTEGER
BEGIN
RETURN (SELECT COUNT(*) FROM DRL_result WHERE eventID = p_eventId);
END$$
DELIMITER ;

USE `ringingV2`;
DROP function IF EXISTS `f_team_faultDifferenceByEvent`;

DELIMITER $$
USE `ringingV2`$$
CREATE FUNCTION `f_team_faultDifferenceByEvent` (
    p_eventID INT,
    p_result FLOAT
    )
    RETURNS FLOAT
BEGIN
RETURN (SELECT SUM(faults) - (p_result * f_event_numberOfTeams(p_eventID))
        FROM DRL_result
        WHERE eventID = p_eventID);
END$$

DELIMITER ;

USE `ringingV2`;
DROP function IF EXISTS `f_team_competitionCountByYear`;

DELIMITER $$
USE `ringingV2`$$
CREATE FUNCTION `f_team_competitionCountByYear` (
    p_teamID INT,
    p_year YEAR
    )
    RETURNS INTEGER
BEGIN

RETURN (SELECT COUNT(*) AS competitions
        FROM DRL_result r
                 INNER JOIN DRL_event e ON r.eventID = e.ID AND e.year = p_year
        WHERE teamID = p_teamID);
END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS`sp_competition_summaryById`$$

CREATE PROCEDURE `sp_competition_summaryById`(
    IN p_competitionID INT
    )
BEGIN

SELECT
    e.competitionID,
    c.competitionName,
    competitionCount.competitionCount,
    MIN(r.faults) lowestFaults,
    MAX(r.faults) highestFaults,
    ROUND(AVG(r.faults), 2) meanFaults,
    medians.medianFaults,
    ROUND(teamCount.teams/competitionCount.competitionCount, 2) AS aveEntry,
    DQs.DQs
FROM
    DRL_event e
        INNER JOIN DRL_result r ON e.id = r.eventID
        INNER JOIN DRL_competition c ON e.competitionID = c.ID AND competitionID = p_competitionID,
    (
        SELECT
            COUNT(*) AS competitionCount
        FROM
            DRL_event
        WHERE competitionID = p_competitionID
    ) competitionCount,
    (
        SELECT
            COUNT(*) AS teams
        FROM
            DRL_result DRLr
                INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
        WHERE
                DRLe.competitionID = p_competitionID
    ) teamCount,
    (
        SELECT
            COUNT(*) AS DQs
        FROM
            DRL_result DRLr
                INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
        WHERE
                DRLe.competitionID = p_competitionID
          AND DRLr.faults = 0
    ) DQs,
    (
        SELECT
            ROUND(AVG(m.faults), 2) AS medianFaults
        FROM (
                 SELECT
                     a.faults
                 FROM (
                          SELECT
                              DRLr.faults,
                              DRLe.competitionID
                          FROM
                              DRL_result DRLr
                                  INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                          WHERE
                                  competitionID = p_competitionID
                          ORDER BY competitionID
                      ) a,
                      (
                          SELECT
                              DRLr.faults,
                              DRLe.competitionID
                          FROM
                              DRL_result DRLr
                                  INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                          WHERE
                                  competitionID = p_competitionID
                          ORDER BY competitionID
                      ) b
                 WHERE
                         a.competitionID = b.competitionID
                 GROUP BY a.faults
                 HAVING SUM(CASE WHEN a.faults = b.faults THEN 1 ELSE 0 END)
                            >= ABS(SUM(SIGN(a.faults - b.faults)))
             ) m
    ) medians
WHERE r.faults != 0
GROUP BY competitionCount, medianFaults, teams, DQs;

END$$

DROP PROCEDURE IF EXISTS `sp_competition_winningTeamSummaryById`$$
CREATE DEFINER=`homestead`@`%` PROCEDURE `sp_competition_winningTeamSummaryById`(
    IN p_competitionID INT
)
BEGIN
    SELECT
        e.competitionID,
        c.competitionName,
        counts.competitionCount,
        MIN(r.faults) lowestWinningFaults,
        MAX(r.faults) highestWinningFaults,
        ROUND(AVG(r.faults), 2) meanFaults,
        medians.medianFaults
    FROM
        DRL_event e
            INNER JOIN DRL_result r ON e.id = r.eventID
            INNER JOIN DRL_competition c ON e.competitionID = c.ID
            AND c.ID = p_competitionID
            INNER JOIN (
            SELECT
                COUNT(*) AS competitionCount,
                competitionID
            FROM
                DRL_event
            WHERE competitionID = p_competitionID
            GROUP BY competitionID
        ) counts ON e.competitionID = counts.competitionID
            INNER JOIN (
            SELECT
                ROUND(AVG(m.faults), 2) AS medianFaults,
                m.competitionID
            FROM (
                     SELECT
                         a.faults,
                         a.competitionID
                     FROM (
                              SELECT
                                  DRLr.faults,
                                  DRLe.competitionID
                              FROM
                                  DRL_result DRLr
                                      INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                                      AND DRLe.competitionID = p_competitionID
                              WHERE
                                      DRLr.position = 1
                              ORDER BY competitionID
                          ) a,
                          (
                              SELECT
                                  DRLr.faults,
                                  DRLe.competitionID
                              FROM
                                  DRL_result DRLr
                                      INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                                      AND DRLe.competitionID = p_competitionID
                              WHERE
                                      DRLr.position = 1
                              ORDER BY competitionID
                          ) b
                     WHERE
                             a.competitionID = b.competitionID
                     GROUP BY a.competitionID, a.faults
                     HAVING SUM(CASE WHEN a.faults = b.faults THEN 1 ELSE 0 END)
                                >= ABS(SUM(SIGN(a.faults - b.faults)))
                 ) m
            GROUP BY competitionID
        ) medians ON e.competitionID = medians.competitionID
    WHERE r.position = 1
    GROUP BY e.competitionID;
END$$
DROP procedure IF EXISTS `sp_team_pointsAndRankByYear`$$

CREATE PROCEDURE `sp_team_pointsAndRankByYear` (
    IN p_teamID INT,
    IN p_year YEAR
)
BEGIN
    SELECT
        t.teamName AS team,
        COUNT(r.position) AS no_of_comps,
        SUM(r.points) AS tot_points,
        AVG(r.points) AS ranking,
        e.year AS year
    FROM
        DRL_result r
            INNER JOIN DRL_event e ON r.eventID = e.id
            INNER JOIN team t ON r.teamID = t.ID
    WHERE
            r.teamID = p_teamID
      AND e.year = p_year
    GROUP BY r.teamID , e.year;
END$$

    DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_event_winnerById`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_event_winnerById` (
    IN p_eventID INT
    )
BEGIN
SELECT
    r.faults AS faults,
    t.teamName AS team,
    r.eventID AS eventID,
    e.year AS year,
    c.competitionName AS competition,
    l.location
FROM
    DRL_result r
        INNER JOIN team t ON r.teamID = t.ID
        INNER JOIN DRL_event e ON r.eventID = e.ID AND e.ID = p_eventID
        INNER JOIN DRL_competition c ON e.competitionID = c.ID
        INNER JOIN location l ON e.locationID = l.ID
WHERE
        r.position = 1;
END$$

DELIMITER ;
USE `ringingV2`;
DROP procedure IF EXISTS `sp_competition_winningTeamListById`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_competition_winningTeamListById` (
    IN p_competitionID INT
    )
BEGIN
SELECT
    r.faults AS faults,
    t.teamName AS team,
    r.eventID AS eventID,
    e.year AS year,
    c.competitionName AS competition,
    l.location
FROM
    DRL_result r
        INNER JOIN team t ON r.teamID = t.ID
        INNER JOIN DRL_event e ON r.eventID = e.ID
        AND e.competitionID = p_competitionID
        INNER JOIN DRL_competition c ON e.competitionID = c.ID
        INNER JOIN location l ON e.locationID = l.ID
WHERE r.position = 1
ORDER BY year ASC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_list_teamWinCount`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_list_teamWinCount` ()
BEGIN
SELECT
    COUNT(r.position) AS wins,
    t.teamName AS team,
    e.year AS year
FROM
    DRL_result r
        INNER JOIN DRL_event e ON r.eventID = e.ID
        INNER JOIN team t ON r.teamID = t.ID
WHERE
        r.position = 1
GROUP BY t.teamName, e.year
ORDER BY wins DESC, year ASC;
END$$

DELIMITER ;


USE `ringingV2`;
DROP procedure IF EXISTS `ringingV2`.`sp_list_disqualifications`;

DELIMITER $$
USE `ringingV2`$$
CREATE DEFINER=`homestead`@`%` PROCEDURE `sp_list_disqualifications`()
BEGIN
SELECT r.faults,
       t.teamName,
       c.competitionName,
       l.location,
       e.year
FROM DRL_result r
         INNER JOIN DRL_event e ON r.eventID = e.id
         INNER JOIN DRL_competition c ON e.competitionID = c.id
         INNER JOIN location l ON e.locationID = l.id
         INNER JOIN team t ON r.teamID = t.id
WHERE r.faults=0
ORDER BY year ASC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_list_annualFaultDifferences`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_list_annualFaultDifferences` ()
BEGIN
SELECT
    t.teamName AS team,
    e.year AS year,
    SUM(f_team_faultDifferenceByEvent(e.id, r.faults)) AS fault_diff
FROM
    DRL_result r
        INNER JOIN DRL_event e ON r.eventID = e.ID
        INNER JOIN team t ON r.teamID = t.ID
GROUP BY team, year
ORDER BY fault_diff DESC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_list_allTimeFaultDifferences`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_list_allTimeFaultDifferences` ()
BEGIN
SELECT
    t.teamName AS team,
    SUM(f_team_faultDifferenceByEvent(e.id, r.faults)) AS fault_diff
FROM
    DRL_result r
        INNER JOIN DRL_event e ON r.eventID = e.ID
        INNER JOIN team t ON r.teamID = t.ID
GROUP BY team
ORDER BY fault_diff DESC;
END$$

DELIMITER ;



USE `ringingV2`;
DROP procedure IF EXISTS `sp_result_allComersTableByYear`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_result_allComersTableByYear` (
    IN p_year INT
    )
BEGIN
SELECT
    t.teamName AS team,
    f_team_competitionCountByYear(t.id, p_year) AS comps,
    ROUND(SUM(f_team_faultDifferenceByEvent(e.id, r.faults)), 2) AS faultDiff,
    ROUND(SUM(r.points)/f_team_competitionCountByYear(t.id, p_year), 2) AS ranking
FROM
    team t
        INNER JOIN DRL_result r ON t.id = r.teamID
        INNER JOIN DRL_event e ON r.eventID = e.id AND e.year = p_year
GROUP BY team, comps, t.id
ORDER BY ranking DESC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_result_5plusTableByYear`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_result_5plusTableByYear` (
    IN p_year YEAR
    )
BEGIN
SELECT
    t.teamName AS team,
    f_team_competitionCountByYear(t.id, p_year) AS comps,
    ROUND(SUM(f_team_faultDifferenceByEvent(e.id, r.faults)), 2) AS faultDiff,
    ROUND(SUM(r.points)/f_team_competitionCountByYear(t.id, p_year), 2) AS ranking
FROM team t
         INNER JOIN DRL_result r ON t.id = r.teamID
         INNER JOIN DRL_event e ON r.eventID = e.id AND e.year = p_year
WHERE f_team_competitionCountByYear(t.id, p_year) >= 5
GROUP BY team, comps, t.id
ORDER BY ranking DESC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_team_annualTableRows`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_team_annualTableRows` (
    IN p_teamID INT
    )
BEGIN
SELECT
    e.year AS year,
    f_team_competitionCountByYear(t.id, e.year) AS comps,
    ROUND(SUM(f_team_faultDifferenceByEvent(e.id, r.faults)), 2) AS faultDiff,
    ROUND(SUM(r.points)/f_team_competitionCountByYear(t.id, e.year), 2) AS ranking
FROM team t
         INNER JOIN DRL_result r ON t.id = r.teamID
         INNER JOIN DRL_event e ON r.eventID = e.id
WHERE t.id = p_teamID
GROUP BY comps, t.id, e.year
ORDER BY year ASC;
END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_competition_summaryList`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_competition_summaryList` (
    IN p_competitionId INT
    )
BEGIN

SELECT
    l.location,
    e.year,
    f_event_numberOfTeams(e.id) AS entry,
    SUM(r.faults) AS totalFaults,
    ROUND(AVG(r.faults), 2) AS meanFaults,
    medians.medianFaults,
    winner.teamName,
    firstPlace.faults AS winningFaults,
    secondPlace.faults - firstPlace.faults AS winningMargin
FROM
    DRL_competition c
        INNER JOIN DRL_event e ON c.id = e.competitionId
        INNER JOIN location l ON e.locationId = l.id
        INNER JOIN DRL_result firstPlace ON e.id = firstPlace.eventId AND firstPlace.position = 1
        INNER JOIN team winner ON firstPlace.teamId = winner.id
        INNER JOIN DRL_result secondPlace ON e.id = secondPlace.eventId AND secondPlace.position = 2
        INNER JOIN DRL_result r ON e.id = r.eventID
        INNER JOIN (
        SELECT
            ROUND(AVG(m.faults), 2) AS medianFaults,
            eventId
        FROM (
                 SELECT
                     a.faults,
                     a.eventId
                 FROM (
                          SELECT
                              DRLr.faults,
                              DRLe.competitionID,
                              DRLe.id AS eventId
                          FROM
                              DRL_result DRLr
                                  INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                          WHERE
                                  competitionID = p_competitionID
                          ORDER BY competitionID
                      ) a,
                      (
                          SELECT
                              DRLr.faults,
                              DRLe.competitionID,
                              DRLe.id AS eventId
                          FROM
                              DRL_result DRLr
                                  INNER JOIN DRL_event DRLe ON DRLr.eventID = DRLe.id
                          WHERE
                                  competitionID = p_competitionID
                          ORDER BY competitionID
                      ) b
                 WHERE
                         a.competitionID = b.competitionID AND
                         a.eventId = b.eventId
                 GROUP BY a.faults, a.eventId
                 HAVING SUM(CASE WHEN a.faults = b.faults THEN 1 ELSE 0 END)
                            >= ABS(SUM(SIGN(a.faults - b.faults)))
             ) m
        GROUP BY eventID
    ) medians ON e.id = medians.eventId
WHERE c.id = p_competitionID
GROUP BY location, year, entry, teamName, winningFaults, winningMargin, medianFaults
ORDER BY year ASC;

END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_list_teamSummary`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_list_teamSummary` ()
BEGIN
SELECT
    t.teamName,
    MIN(e.year) AS firstYear,
    MAX(e.year) AS lastYear,
    GROUP_CONCAT(DISTINCT e.year ORDER BY year ASC) AS yearsEntered,
    COUNT(DISTINCT e.year) AS yearsEntered,
    SUM(r.faults) AS totFaults,
    ROUND(AVG(r.faults),2) AS aveFaults,
    ROUND(SUM(r.faults)/COUNT(DISTINCT e.year),2) AS faultsPerYear,
    COUNT(r.faults) AS competitionsEntered,
    COUNT(winners.faults) AS competitionWins
FROM
    DRL_event e
        INNER JOIN DRL_result r ON e.id = r.eventId
        INNER JOIN team t ON r.teamId = t.id
        LEFT JOIN DRL_result winners ON e.id = winners.eventId AND winners.position = 1 AND winners.teamId = r.teamId
GROUP BY t.teamName;

END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_team_allTimeSummary`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_team_allTimeSummary` (
    IN p_teamId INT
    )
BEGIN
SET @@group_concat_max_len = 500;
SELECT
    t.teamName,
    MIN(e.year) AS firstYear,
    MAX(e.year) AS lastYear,
    GROUP_CONCAT(DISTINCT e.year ORDER BY year ASC) AS yearsEntered,
    COUNT(DISTINCT e.year) AS yearsEntered,
    ROUND(AVG(r.position),2) AS avePosition,
    SUM(r.faults) AS totFaults,
    ROUND(AVG(r.faults),2) AS aveFaults,
    ROUND(SUM(r.faults)/COUNT(DISTINCT e.year),2) AS faultsPerYear,
    COUNT(r.faults) AS competitions,
    ROUND(COUNT(r.faults)/COUNT(DISTINCT e.year),2) AS competitionsPerYear,
    COUNT(winners.faults) AS competitionWins,
    ROUND(COUNT(r.faults)/COUNT(DISTINCT e.year),2) AS winsPerYear,
    SUM(r.points) AS totRankingPoints,
    ROUND(SUM(r.points)/COUNT(DISTINCT e.year), 2) AS rankingPointsPerYear,
    ROUND(SUM(f_team_faultDifferenceByEvent(r.eventId, r.faults)), 2) AS faultDifference,
    ROUND(SUM(f_team_faultDifferenceByEvent(r.eventId, r.faults))/COUNT(DISTINCT e.year), 2) AS faultDifferencePerYear
FROM
    DRL_event e
        INNER JOIN DRL_result r ON e.id = r.eventId AND r.teamId = 335
        INNER JOIN team t ON r.teamId = t.id
        LEFT JOIN DRL_result winners ON e.id = winners.eventId AND winners.position = 1 AND winners.teamId = r.teamId
GROUP BY t.teamName;

END$$

DELIMITER ;

USE `ringingV2`;
DROP procedure IF EXISTS `sp_team_annualSummaryByYear`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_team_annualSummaryByYear` (
    IN p_teamId INT,
    IN p_year YEAR
    )
BEGIN
SELECT
    t.teamName,
    e.year,
    SUM(r.faults) AS totFaults,
    ROUND(AVG(r.faults),2) AS aveFaults,
    SUM(r.points) AS totRankingPoints,
    ROUND(AVG(r.points),2) AS ranking,
    COUNT(r.faults) AS competitionsEntered,
    COUNT(winners.faults) AS competitionWins,
    ROUND(SUM(f_team_faultDifferenceByEvent(r.eventID, r.faults)), 2) AS faultDifference
FROM
    DRL_event e
        INNER JOIN DRL_result r ON e.id = r.eventId AND r.teamId = p_teamId
        INNER JOIN team t ON r.teamId = t.id
        LEFT JOIN DRL_result winners ON e.id = winners.eventId AND winners.position = 1 AND winners.teamId = r.teamId
WHERE
        e.year = p_year
GROUP BY t.teamName, e.year;
END$$

DELIMITER ;


USE `ringingV2`;
DROP procedure IF EXISTS `sp_team_annualSummaryList`;

DELIMITER $$
USE `ringingV2`$$
CREATE PROCEDURE `sp_team_annualSummaryList` (
    IN p_teamId INT
    )
BEGIN
SELECT
    t.teamName,
    e.year,
    SUM(r.faults) AS totFaults,
    ROUND(AVG(r.faults),2) AS aveFaults,
    SUM(r.points) AS totRankingPoints,
    ROUND(AVG(r.points),2) AS ranking,
    COUNT(r.faults) AS competitionsEntered,
    COUNT(winners.faults) AS competitionWins,
    ROUND(SUM(f_team_faultDifferenceByEvent(r.eventID, r.faults)), 2) AS faultDifference
FROM
    DRL_event e
        INNER JOIN DRL_result r ON e.id = r.eventId AND r.teamId = p_teamId
        INNER JOIN team t ON r.teamId = t.id
        LEFT JOIN DRL_result winners ON e.id = winners.eventId AND winners.position = 1 AND winners.teamId = r.teamId
GROUP BY t.teamName, e.year;
END$$

DELIMITER ;

