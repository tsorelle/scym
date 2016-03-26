CREATE OR REPLACE VIEW attendersReportView AS
  SELECT
    r.year,
    r.registrationCode,
    r.confirmed,
    a.attenderID     AS attenderId,
    a.registrationId AS registrationId,
    a.affiliationCode  AS affiliationCode,
    a.firstName      AS firstName,
    IFNULL(a.middleName,'')  AS middleName,
    a.lastName       AS lastName,
    a.attended       AS attended,
    a.creditTypeId      AS feeCredit,
    a.arrivalTime    AS arrivalTime,
    a.departureTime  AS departureTime,
    a.generationId     AS generationId,
    a.housingTypeId AS housingPreference,
    a.firstTimer,
    IF(ISNULL(a.affiliationCode),0,1) AS ScymMembership,
    CONCAT(a.lastName,', ',a.firstName,IF(ISNULL(a.middleName),'',CONCAT(' ',a.middleName))) AS AttenderName,
    IF(ISNULL(m.meetingName),IF(ISNULL(a.otherAffiliation),'(unknown)',a.otherAffiliation),m.meetingName) AS Affiliation,
    IF((a.firstTimer = 1),'Yes','No') AS FirstTimeAttender,
    ScymTime(a.arrivalTime)  AS Arrival,
    ScymTime(a.departureTime)  AS Departure,
    IF((a.attended = 1),'Yes','No') AS CheckedIn,
    (CASE a.generationId WHEN 1 THEN 'Adult' WHEN 2 THEN 'Youth' WHEN 4 THEN 'Baby' ELSE '' END) AS Generation,
    (CASE a.creditTypeId WHEN 2 THEN 'Teacher' WHEN 4 THEN 'Staff' ELSE '' END) AS Role,
    IF(ha.housingAssignmentId IS NULL,'(not assigned)',IF(COUNT( DISTINCT ha.housingUnitId) > 1,CONCAT(hu.unitname,' *'),hu.unitname)) AS housing,
    IFNULL(a.notes,'') AS notes
  FROM attenders a
    JOIN registrations r ON r.registrationId = a.registrationId
    LEFT OUTER JOIN meetings m ON (a.affiliationCode = m.affiliationCode)
    LEFT OUTER JOIN housingassignments ha ON a.attenderID = ha.attenderId
    LEFT OUTER JOIN housingunits hu ON ha.housingUnitId = hu.housingUnitId
  GROUP BY a.attenderId;

-- SELECT * FROM attendersReportView;
CREATE OR REPLACE VIEW currentAttendersReportView AS
  SELECT * FROM attendersReportView av
  WHERE av.year =  currentYmYear();
