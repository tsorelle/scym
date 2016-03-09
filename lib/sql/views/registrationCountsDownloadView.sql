-- dependsOn: attenderCountsView and ledgerView

CREATE OR REPLACE VIEW registrationCountsDownloadView AS
SELECT  ledger.year, ledger.registrationId, ledger.registrationCode, ledger.Name,
COUNT(a.attenderId) AS Attenders,
(SELECT COUNT(*) FROM attenders ac WHERE ac.registrationId = ledger.registrationId AND generationId = 1 AND attended = 1) AS Adults,
(SELECT COUNT(*) FROM attenders ac WHERE ac.registrationId = ledger.registrationId AND generationId = 2 AND attended = 1) AS Youth,
IFNULL((SELECT SUM(ac.Meals) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND ac.DayVisitor = 'Yes'),0)  AS Meals,
IFNULL((SELECT (SUM(amount) DIV ft.unitAmount) FROM charges c JOIN feetypes ft ON ft.feeTypeID = c.feeTypeID WHERE c.registrationId = ledger.registrationId AND feeCode = 'LINEN'),0) AS Linens,
IFNULL((SELECT SUM(Days) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND DayVisitor = 'Yes' AND generation = 'Adult'),0) AS AdultDays,
IFNULL((SELECT SUM(Days) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND DayVisitor = 'Yes' AND generation = 'Youth'),0) AS YouthDays,
IFNULL((SELECT SUM(DormNights) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND generation = 'Adult'),0) AS AdultDormNights,
IFNULL((SELECT SUM(DormNights) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND generation = 'Youth'),0) AS YouthDormNights,
IFNULL((SELECT SUM(CabinNights) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND generation = 'Adult'),0) AS AdultCabinNights,
IFNULL((SELECT SUM(CabinNights) FROM attenderCountsView ac WHERE ac.registrationId = ledger.registrationId AND generation = 'Youth'),0) AS YouthCabinNights,
ledger.Fees, ledger.Donations, ledger.Credits, ledger.Payments,((ledger.Fees + ledger.Donations) - (ledger.Credits + ledger.Payments)) AS Balance
FROM ledgerView ledger
JOIN attenders a ON ledger.registrationId = a.registrationId
WHERE a.attended = 1
GROUP BY ledger.year, ledger.registrationId;

-- SELECT * FROM registrationCountsDownloadView;