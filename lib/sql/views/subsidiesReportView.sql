CREATE OR REPLACE VIEW subsidiesReportView AS
  SELECT r.year, r.registrationId, a.attenderID AS attenderId, a.creditTypeId, ct.creditTypeName AS role,
         r.name AS registrationName, r.registrationCode,
         FormatName(a.firstName,a.middleName,a.lastName) AS attenderName,
         CONCAT(TRIM(LOWER(a.lastName)),',',TRIM(LOWER(a.firstName)),
                IF(a.middleName IS NULL,'',CONCAT(' ',TRIM(LOWER(a.middleName))))) AS sortName,
         IF(a.attended = 1,'Yes','No') AS attended
  FROM registrations r
    JOIN attenders a ON r.registrationId = a.registrationId
    JOIN credittypes ct ON ct.creditTypeId = a.creditTypeId;

-- SELECT * FROM subsidiesReportView;