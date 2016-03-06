CREATE OR REPLACE VIEW housingCountsDetailView AS
SELECT a.attenderId, a.lastName, a.firstName, a.generationId, hu.unitname,  ht.category,
IF (ht.category=1,'Dorm','Cabin/Motel') AS CampCategory,
COUNT(h.housingAssignmentId) AS nights -- h.day,
FROM housingassignments h
JOIN attenders a ON h.attenderId = a.attenderId
JOIN housingunits hu ON h.housingUnitId = hu.housingUnitId
JOIN housingtypes ht ON hu.housingTypeId = ht.housingTypeID
WHERE generationId < 3 AND attended = 1
GROUP BY category, firstname, lastname,h.housingUnitId
ORDER BY category,nights;

CREATE OR REPLACE VIEW attenderCountsView AS
SELECT r.year, a.attenderId, a.registrationId,
IF(a.attended = 1 ,'Yes','No') AS attended,
FormatName(a.firstName, a.middleName, a.lastName) AS NAME,
ScymTime(arrivalTime) AS Arrival,
ScymTime(departureTime) AS Departure,
(CASE a.generationId
   WHEN 1 THEN 'Adult'
   WHEN 2 THEN 'Youth'
   WHEN 3 THEN 'Baby'
   ELSE '?'
END) AS Generation,
(CASE a.creditTypeId
   WHEN 2 THEN 'Teacher'
   WHEN 3 THEN 'Guest'
   WHEN 4 THEN 'Staff'
   ELSE ''
END) AS Role,
(departureTime DIV 10 - arrivalTime DIV 10 + 1) AS Days,
(departureTime DIV 10 - arrivalTime DIV 10) AS Nights,
IFNULL((SELECT SUM(nights) FROM housingCountsDetailView WHERE attenderid = a.attenderid AND category = 1),0) AS DormNights,
IFNULL((SELECT SUM(nights) FROM housingCountsDetailView WHERE attenderid = a.attenderid AND category > 1), 0) AS CabinNights,
(SELECT COUNT(*) FROM meals WHERE attenderid = a.attenderid) AS Meals,
IF(a.housingTypeId = 1,'Yes','No') AS DayVisitor
FROM attenders a
JOIN registrations r ON a.registrationId = r.registrationId;

-- SELECT * FROM attenderCountsView;
