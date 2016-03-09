-- depends on ledgerView

CREATE OR REPLACE VIEW ledgerDownloadView AS
  SELECT ledger.*,
    (ledger.Fees + ledger.Donations - ledger.Credits - ledger.Payments) AS Balance,
    IF(ledger.attendedCount > 0,'Yes','No') AS attended
  FROM ledgerView ledger;

-- SELECT * FROM ledgerDownloadView;