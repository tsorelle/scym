DROP VIEW IF EXISTS occupantsView;
CREATE VIEW occupantsView AS
  SELECT
    r.year, ScymNumberToWeekday(ha.day) AS 'day',
            ha.day AS dayNumber, a.attenderId, hu.`unitname`, FormatName(a.firstName,a.middleName,a.lastName) AS NAME,
            hu.`unitname` AS unit, IF (a.attended = 1, 'Yes', 'No') AS arrived
  FROM housingUnits hu
    JOIN housingAssignments ha ON hu.`housingUnitId` = ha.housingUnitId
    JOIN attenders a ON a.attenderId = ha.attenderId
    JOIN registrations r ON a.registrationId = r.registrationId
  ORDER BY r.year,ha.day,hu.unitname;

