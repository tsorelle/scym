CREATE OR REPLACE VIEW currentRegistrationsView AS
  SELECT
    r.*,
    (SELECT COUNT(*) FROM attenders a WHERE a.registrationId = r.registrationId AND a.attended = 1) AS attendedCount,
    IF((SELECT COUNT(*) FROM attenders a WHERE a.registrationId = r.registrationId AND a.attended = 1) > 0,'Yes','No') AS attended
  FROM registrations r
  WHERE r.active = 1 AND r.statusId > 1
        AND r.year = currentYmYear();

-- select * from currentRegistrationsView;

