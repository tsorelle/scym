DROP VIEW IF EXISTS currentHousingAssignments;
CREATE VIEW currentHousingAssignments AS
SELECT ha.*,r.registrationId,a.FirstName,a.LastName
FROM housingAssignments ha
  JOIN attenders a ON ha.`attenderId` = a.`attenderID`
  JOIN registrations r ON a.`registrationId` = r.`registrationId`
WHERE r.`year` IN
      (SELECT a.`year` FROM annualsessions a
      WHERE a.`start` >= CURRENT_DATE() AND a.`end` <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) );
