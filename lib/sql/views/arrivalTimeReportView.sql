DROP VIEW IF EXISTS arrivalTimeSummaryView;
CREATE VIEW arrivalTimeSummaryView AS
  SELECT
    r.year,a.arrivalTime, ScymTime(a.arrivalTime) AS arrivalText, COUNT(attenderId) AS arrivalCount FROM attenders a
    INNER JOIN registrations r ON (a.registrationid = r.registrationid AND r.active = 1)
  GROUP BY r.year,a.arrivalTime;

-- SELECT * FROM arrivalTimeSummaryView;