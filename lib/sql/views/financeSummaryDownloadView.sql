-- depends on ledgerDownloadView,ledgerDetailView
CREATE OR REPLACE VIEW currentLedgerView AS
SELECT * FROM ledgerDownloadView r WHERE
    r.attended = 'Yes' AND
    r.year IN
      (SELECT a.year FROM annualsessions a
      WHERE a.start >= CURRENT_DATE() AND a.end <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) );

SELECT * FROM currentLedgerView;

CREATE OR REPLACE VIEW currentLedgerDetailView AS
SELECT * FROM ledgerDetailView r WHERE
        r.attendedCount > 0 AND
        r.year IN
          (SELECT a.year FROM annualsessions a
          WHERE a.start >= CURRENT_DATE() AND a.end <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) );

SELECT * FROM currentLedgerDetailView;


CREATE OR REPLACE VIEW financeSummaryDownloadView AS
SELECT detail.ItemGroup,
     CONCAT(CAST(detail.`EntryType` AS CHAR CHARACTER SET utf8),': ', CAST(detail.Type AS CHAR CHARACTER SET utf8)) AS Item,
    SUM(detail.`value`) AS Amount,
    COUNT(*) AS 'Count'
FROM currentLedgerDetailView AS detail
GROUP BY  ItemGroup, detail.`Type`

UNION
SELECT detail.ItemGroup,
    CONCAT('Total ',detail.EntryType,'s') AS Item,
    SUM(detail.`value`) AS Amount,
    COUNT(*) AS 'Count'
FROM currentLedgerDetailView AS detail
GROUP BY  ItemGroup

UNION
SELECT 5 AS ItemGroup, 'Total debit transactions' AS Item,
	(SELECT SUM(detail.`value`) FROM currentLedgerDetailView detail WHERE detail.TransactionType = 'debit') AS Amount,
    (SELECT COUNT(*) FROM currentLedgerDetailView detail WHERE detail.TransactionType = 'debit') AS 'Count'

UNION
SELECT 6 AS ItemGroup, 'Total credit transactions' AS Item,
	(SELECT SUM(detail.value) FROM currentLedgerDetailView detail WHERE TransactionType = 'credit') AS Amount,
	(SELECT COUNT(*) FROM currentLedgerDetailView detail WHERE detail.TransactionType = 'credit') AS 'Count'

UNION
SELECT 7 AS ItemGroup, 'Amount still due' AS Item,
    (SELECT SUM(ledger.Balance) FROM currentLedgerView ledger WHERE ledger.Balance > 0.0) AS Amount,
	(SELECT COUNT(*) FROM currentLedgerView ledger WHERE ledger.Balance > 0.0) AS 'Count'

UNION
SELECT 8 AS ItemGroup, 'Refunds due' AS Item,
    (SELECT ABS(SUM(ledger.Balance)) FROM currentLedgerView ledger WHERE ledger.Balance < 0.0) AS Amount,
	(SELECT COUNT(*) FROM currentLedgerView ledger WHERE ledger.Balance < 0.0) AS 'Count';

SELECT * FROM  financeSummaryDownloadView;