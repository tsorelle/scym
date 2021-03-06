CREATE OR REPLACE VIEW housingAvailabilityView AS
  SELECT u.housingUnitId, ha.day, IFNULL(u.capacity,0) AS capacity, COUNT(*) AS occupants
  FROM housingunits u
    JOIN housingassignments ha ON ha.housingUnitId = u.housingUnitId
    JOIN attenders a ON ha.attenderId = a.attenderID
    JOIN registrations r ON a.registrationId = r.registrationId
  WHERE r.year IN
        (SELECT a.year FROM annualsessions a
        WHERE a.start >= CURRENT_DATE() AND a.end <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) )
  GROUP BY u.housingUnitId, ha.day;
