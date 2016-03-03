
CREATE OR REPLACE VIEW transactionsDownloadView AS
SELECT
r.year,r.registrationId,r.registrationCode, r.name AS 'Name', r.attended,
'debit' AS 'TransactionType',
'Charge' AS Entry,
f.description AS 'Description',
c.amount AS 'Amount'
FROM charges c
JOIN currentRegistrationsView r ON r.registrationId = c.registrationId
JOIN feetypes f ON f.feeTypeId = c.feeTypeID

UNION
SELECT
r.year,r.registrationId,r.registrationCode, r.name AS 'Name', r.attended,
'debit' AS 'TransactionType',
'Donation' AS Entry,
dt.fundName AS 'Description',
d.amount AS 'Amount'
FROM donations d
JOIN currentRegistrationsView r ON r.registrationId = d.registrationId
JOIN donationtypes dt ON d.donationTypeId = dt.donationTypeId

UNION
SELECT
r.year,r.registrationId,r.registrationCode, r.name AS 'Name', r.attended,
'credit' AS 'TransactionType',
'Credit' AS Entry,
CONCAT(crt.creditTypeName,': ',cr.description)  AS 'Description',
cr.amount AS 'Amount'
FROM credits cr
JOIN currentRegistrationsView r ON r.registrationId = cr.registrationId
JOIN credittypes crt ON cr.creditTypeId = crt.creditTypeId

UNION
SELECT
r.year,r.registrationId,r.registrationCode, r.name AS 'Name', r.attended,
'credit' AS 'TransactionType',
'Payment' AS Entry,
IF(p.paymentType = 1, 'Cash',CONCAT('#',p.checkNumber,' ',p.payor)) AS 'Description',
p.amount AS 'Amount'
FROM payments p
JOIN currentRegistrationsView r ON r.registrationId = p.registrationId;

SELECT * FROM transactionsDownloadView;

