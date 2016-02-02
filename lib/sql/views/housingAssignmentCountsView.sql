-- Depends on currentHousingAssignments view
DROP VIEW IF EXISTS housingAssignmentCountsView;
CREATE VIEW housingAssignmentCountsView AS
  SELECT
    u.unitname, t.housingTypeDescription,u.capacity,
    (SELECT COUNT(DISTINCT ha.attenderId) FROM currentHousingAssignments ha WHERE ha.day = 4 AND ha.housingUnitId = u.housingUnitId) AS Thursday,
    (SELECT COUNT(DISTINCT ha.attenderId) FROM currentHousingAssignments ha WHERE ha.day = 5 AND ha.housingUnitId = u.housingUnitId) AS Friday,
    (SELECT COUNT(DISTINCT ha.attenderId) FROM currentHousingAssignments ha WHERE ha.day = 6 AND ha.housingUnitId = u.housingUnitId) AS Saturday
  FROM housingunits u
    JOIN housingtypes t ON t.housingTypeId = u.housingTypeId
  ORDER BY u.unitname;

