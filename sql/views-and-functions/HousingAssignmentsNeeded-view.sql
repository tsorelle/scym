CREATE VIEW HousingAssignmentsNeeded AS
SELECT DISTINCT a.attenderId, a.registrationId,COUNT(DISTINCT ha.`day`) AS assignments,((departureTime DIV 10) - (arrivalTime DIV 10)) AS nights
FROM attenders a
JOIN housingTypes ht ON ht.`housingTypeID` = a.`housingTypeId`
LEFT OUTER JOIN housingassignments ha ON a.`attenderID` = ha.`attenderID`
WHERE ht.`housingTypeCode` NOT IN ('NONE','TENT')
GROUP BY a.`attenderId`
HAVING assignments < nights;

/*
Usage example. Select registrations needed assignments:

SELECT r.registrationId, r.name , (u.nights - u.assignments) AS assignmentsNeeded
FROM registrations r
JOIN  HousingAssignmentsNeeded u ON u.registrationId = r.registrationID
WHERE r.year = 2016;

 */
