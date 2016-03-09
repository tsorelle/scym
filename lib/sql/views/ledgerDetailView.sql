CREATE OR REPLACE VIEW ledgerDetailView AS
  SELECT
    r.year,
    r.registrationId,
    r.registrationCode,
    r.name,
    (SELECT COUNT(*) FROM attenders a WHERE r.registrationId = a.registrationId AND a.attended = 1) AS attendedCount,
    'Charge' AS EntryType,
    'debit' AS TransactionType,
    1 AS ItemGroup,
    ft.description AS 'Type',
    ft.feeTypeID AS ItemTypeId,
    ch.amount  AS 'value' ,
    FormatUSD(ch.amount,'') AS Amount
  FROM charges ch
    JOIN feetypes ft ON ft.feeTypeID = ch.feeTypeID
    JOIN registrations  r ON r.registrationId = ch.registrationId

  UNION ALL
  SELECT
    r.year,
    r.registrationId,
    r.registrationCode,
    r.name,
    (SELECT COUNT(*) FROM attenders a WHERE r.registrationId = a.registrationId AND a.attended = 1) AS attendedCount,
    'Donation' AS EntryType,
    'debit' AS TransactionType,
    2 AS ItemGroup,
    dt.fundName AS 'Type',
    dt.donationTypeId AS ItemTypeId,
    d.amount  AS 'value' ,
    FormatUSD(d.amount,'') AS Amount
  FROM donations d
    JOIN donationtypes dt ON dt.donationTypeId = d.donationTypeId
    JOIN registrations  r ON r.registrationId = d.registrationId

  UNION ALL
  SELECT
    r.year,
    r.registrationId,
    r.registrationCode,
    r.name,
    (SELECT COUNT(*) FROM attenders a WHERE r.registrationId = a.registrationId AND a.attended = 1) AS attendedCount,
    'Credit' AS EntryType,
    'credit' AS TransactionType,
    3 AS ItemGroup,
    ct.creditTypeName AS 'Type',
    ct.creditTypeId AS ItemTypeId,
    c.amount  AS 'value' ,
    FormatUSD(c.amount,'') AS Amount
  FROM credits c
    JOIN credittypes ct ON ct.creditTypeId = c.creditTypeId
    JOIN registrations  r ON r.registrationId = c.registrationId

  UNION ALL
  SELECT
    r.year,
    r.registrationId,
    r.registrationCode,
    r.name,
    (SELECT COUNT(*) FROM attenders a WHERE r.registrationId = a.registrationId AND a.attended = 1) AS attendedCount,
    'Payment' AS EntryType,
    'credit' AS TransactionType,
    4 AS ItemGroup,
    'Payment' AS 'Type',1 AS ItemTypeId,
    p.amount  AS 'value' ,
    FormatUSD(p.amount,'') AS Amount
  FROM payments p
    JOIN registrations  r ON r.registrationId = p.registrationId;