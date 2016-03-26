CREATE OR REPLACE VIEW currentAttenders AS
SELECT a.*
FROM attenders a
  JOIN registrations r ON a.registrationId = r.registrationId
WHERE r.year = currentYmYear();