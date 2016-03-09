DROP VIEW IF EXISTS currentAttenders;
CREATE VIEW currentAttenders AS
SELECT a.*
FROM attenders a
  JOIN registrations r ON a.`registrationId` = r.`registrationId`
WHERE r.`year` IN
      (SELECT a.`year` FROM annualsessions a
      WHERE a.`start` >= CURRENT_DATE() AND a.`end` <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) );