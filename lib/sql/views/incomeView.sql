CREATE OR REPLACE VIEW incomeView AS
  SELECT r.year, r.registrationId, r.name AS registrationName,
    p.paymentId,
    IF( p.checkNumber IS NULL,'',
       IF(p.checkNumber = 'cash','',p.checkNumber)) AS checkNumber,
    p.paymentType,p.payor,
    p.amount,
    FormatUSD(p.amount,'') AS amountFormatted,
    IF(p.dateAdded IS NULL,'', DATE_FORMAT(p.dateAdded,'%Y-%m-%d')) AS dateAdded,
    IFNULL(p.addedBy, '') AS addedBy,
    IF(p.paymentType = 1,'cash','check') AS 'type',
    IFNULL(p.notes,'') AS notes
  FROM payments p
    JOIN registrations r ON p.registrationId = r.registrationId;