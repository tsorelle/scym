/*
Depends on attenderCountsView, currentAttenders
*/

CREATE OR REPLACE VIEW campReportHousingDetailView AS
SELECT a.generationId, a.`attenderID`,
IF (ht.category=1,'Dorm','Cabin/Motel') AS CampCategory,
COUNT(h.housingAssignmentId) AS nights -- h.day,
FROM housingassignments h
JOIN currentAttenders a ON h.attenderId = a.attenderId
JOIN housingunits hu ON h.housingUnitId = hu.housingUnitId
JOIN housingtypes ht ON hu.housingTypeId = ht.housingTypeId
WHERE generationId < 3 AND attended = 1
GROUP BY a.`attenderID`, ht.`category`,a.`generationId`;


CREATE OR REPLACE VIEW campReportDownloadView AS
SELECT (4 - Nights) AS LineOrder, CONCAT(CampCategory,' ',Nights,' night ') AS Item, COUNT(*) AS 'Count'
FROM campReportHousingDetailView
GROUP BY Nights, CampCategory
UNION
SELECT 4 AS LineOrder, 'Day visitors - Adult' AS Item,

IFNULL((SELECT SUM(Days) FROM attenderCountsView WHERE Generation = 'Adult' AND DayVisitor = 'Yes'),0) AS 'Count'
UNION
SELECT 5 AS LineOrder, 'Day visitors - Youth' AS Item,
IFNULL((SELECT SUM(Days) FROM attenderCountsView WHERE Generation = 'Youth' AND DayVisitor = 'Yes'),0) AS 'Count'
UNION
SELECT 6 AS LineOrder,  'Extra Meals' AS Item,
(SELECT COUNT(*) FROM meals m JOIN currentAttenders a ON m.attenderId = a.attenderId WHERE a.housingTypeId = 1 AND a.attended = 1) AS 'Count'

UNION
SELECT 7 AS LineOrder, 'Linen Bags' AS Item,
(SELECT IFNULL(SUM(amount) DIV ft.unitAmount,0)
FROM charges c
JOIN registrations r ON (c.registrationId = r.registrationId AND r.year = currentYmYear())
JOIN feetypes ft ON ft.feeTypeID = c.feeTypeID
WHERE feeCode = 'LINEN') AS 'Count'
ORDER BY LineOrder;


SELECT * FROM campReportDownloadView;