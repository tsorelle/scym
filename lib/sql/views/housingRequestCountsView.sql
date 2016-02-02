DROP VIEW IF EXISTS housingRequestCountsView;
CREATE VIEW housingRequestCountsView AS
  SELECT
    r.year,
    ha.day AS dayNumber,
    ScymNumberToWeekday(ha.day) AS DAY,
    ha.housingTypeId,
    ht.housingTypeDescription,
    SUM(ha.confirmed) AS confirmed,
    COUNT(ha.attenderId) AS requested
  FROM housingassignments ha
    LEFT JOIN attenders a ON ha.attenderId = a.attenderId
    LEFT JOIN registrations r ON a.registrationId = r.registrationId
    LEFT JOIN housingtypes ht ON ha.housingTypeId = ht.housingTypeId AND ht.housingTypeId <> 1
  WHERE ht.HousingTypeId <> 1 AND a.generationId < 4 AND r.active = 1
  GROUP BY  r.year, ha.day, ha.housingTypeId;
