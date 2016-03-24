CREATE OR REPLACE VIEW financialAidReportView AS
  SELECT
    r.year               AS year,
    r.registrationId     AS registrationId,
    r.name               AS registrationName,
    r.financialAidAmount AS amount,
    FormatUSD(r.financialAidAmount,'')  AS amountFormatted,
    IF(((SELECT COUNT(0) FROM attenders a WHERE ((a.registrationId = r.registrationId) AND (a.attended = 1))) > 0),'Yes','No') AS attended
  FROM registrations r
  WHERE ((r.financialAidAmount IS NOT NULL)
         AND (r.financialAidAmount > 0.00))
  ORDER BY r.name;
