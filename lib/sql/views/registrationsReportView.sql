DROP VIEW IF EXISTS registrationsReportView;
CREATE VIEW registrationsReportViews AS
  SELECT r.year,
    r.registrationId,
    r.registrationCode,
    r.name,
    IF(r.confirmed = 1,'Yes','No') AS confirmed,
    IF(r.receivedDate IS NULL,'',DATE_FORMAT(r.receivedDate,'%Y-%m-%d')) AS receivedDate,
    IFNULL(r.email,'') AS email,
    IFNULL( r.phone, '') AS phone,
    COUNT(*) AS attenders
  FROM registrations r
  JOIN attenders a ON r.registrationId = a.registrationId
  WHERE r.active = 1
  GROUP BY r.registrationId ;