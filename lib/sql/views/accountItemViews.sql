CREATE OR REPLACE VIEW chargesView AS
  SELECT
    c.registrationId,
    c.chargeId AS id,
    'charge' AS itemType,
    c.amount,
    c.feeTypeID AS feeTypeId,
    c.basis,
    f.description AS feeType,
    IFNULL(c.notes,'') AS notes,
    FormatUSD(c.amount,'') AS amountFormatted,
    IFNULL(c.addedBy,'(unknown)') AS addedBy,
    DisplayMediumDate(c.dateAdded,'') AS dateAdded
  FROM charges c JOIN feetypes f ON f.feeTypeID = c.feeTypeID;

CREATE OR REPLACE VIEW paymentsView AS
  SELECT
    p.registrationId,
    p.paymentId AS id,
    'payment' AS itemType,
    p.amount,
    FormatUSD(p.amount,'') AS amountFormatted,
    DisplayMediumDate(p.dateReceived,'') AS dateReceived,
    p.checkNumber,
    p.payor,
    IFNULL(p.notes,'') AS notes,
    IFNULL(p.addedBy,'(unknown)') AS addedBy,
    DisplayMediumDate(p.dateAdded,'') AS dateAdded
  FROM payments p;

CREATE OR REPLACE VIEW creditsView AS
  SELECT
    c.registrationId,
    c.creditId AS id,
    'credit' AS itemType,
    c.description,
    c.amount,
    c.creditTypeId,
    ct.creditTypeName AS creditType,
    ct.description AS creditTypeDescription,
    IFNULL(c.notes,'') AS notes,
    FormatUSD(c.amount,'') AS amountFormatted,
    IFNULL(c.addedBy,'(unknown)') AS addedBy,
    DisplayMediumDate(c.dateAdded,'') AS dateAdded
  FROM credits c JOIN credittypes ct ON c.creditTypeId = ct.creditTypeId;

CREATE OR REPLACE VIEW donationsView AS
  SELECT
    d.registrationId,
    d.donationId AS id,
    'donation' AS itemType,
    d.donationTypeId,
    d.amount,
    dt.fundName AS donationType,
    IFNULL(d.notes,'') AS notes,
    FormatUSD(d.amount,'') AS amountFormatted,
    IFNULL(d.addedBy,'(unknown)') AS addedBy,
    DisplayMediumDate(d.dateAdded,'') AS dateAdded
  FROM donations d JOIN donationtypes dt ON d.donationTypeId = dt.donationTypeId;

CREATE OR REPLACE VIEW accountLookupsView AS
  SELECT
    'donation' AS lookupType,
    dt.`fundName` AS 'Name',
    dt.`donationTypeId` AS 'Value'
  FROM donationtypes dt
  UNION

  SELECT
    'fee' AS lookupType,
    IF(unitAmount = 0, f.`description`,
       IF(f.feeCode = 'MEAL', CONCAT('Meal fee',' (',unitAmount,' ',basis,')') ,
          CONCAT(f.`description`,' (',unitAmount,' ',basis,')'))) AS 'Name',
    f.`feeTypeID` AS 'Value'
  FROM feetypes f
  -- WHERE f.`feeCode` <> 'OTHER'
  WHERE f.`feeCode` IN ('LINEN','MEAL')
  UNION
  SELECT
    'fee' AS lookupType,
    f.description AS 'Name',
    f.`feeTypeID` AS 'Value'
  FROM feetypes f
  WHERE f.`feeCode` = 'OTHER'

  UNION
  SELECT
    'credit' AS lookupType,
    c.`creditTypeName` AS 'Name',
    c.`creditTypeId` AS 'Value'
  FROM
    credittypes c;

/*
 SELECT * FROM accountLookupsView;
 SELECT * FROM creditsView;
 SELECT * FROM paymentsView;
 select * from chargesView;
 SELECT * FROM donationsView;
*/
