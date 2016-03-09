CREATE OR REPLACE VIEW creditsReportView AS
  SELECT r.year, r.registrationId, c.creditTypeId, ct.creditTypeName
    ,r.name AS registrationName, r.registrationCode, c.amount
    ,FormatUSD(c.amount,'') AS amountFormatted
    ,IF((SELECT COUNT(*) FROM attenders a WHERE a.registrationId = r.registrationId AND a.attended = 1) > 0,'Yes','No') AS attended

  FROM registrations r
    JOIN credits c ON c.registrationId = r.registrationId
    JOIN credittypes ct ON ct.creditTypeId = c.creditTypeId
  ORDER BY r.name;
-- SELECT * FROM creditsReportView;