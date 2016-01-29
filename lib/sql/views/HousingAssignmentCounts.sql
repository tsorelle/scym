DROP VIEW IF EXISTS HousingAssignmentCounts;
CREATE VIEW HousingAssignmentCounts AS
SELECT DISTINCT  r.year, a.registrationId, r.registrationCode, r.name, a.attenderId,
  FormatName(a.firstName,a.middleName,a.lastName) AS attenderName,
  COUNT(DISTINCT ha.day) AS assignments,((departureTime DIV 10) - (arrivalTime DIV 10)) AS nights, r.confirmed
FROM attenders a
  JOIN registrations r ON r.registrationID = a.registrationId
  LEFT OUTER JOIN housingassignments ha ON a.attenderID = ha.attenderID
GROUP BY a.attenderId
