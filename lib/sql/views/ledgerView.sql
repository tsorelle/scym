CREATE OR REPLACE VIEW ledgerView AS
  SELECT
    r.`year`,
    `r`.`registrationId` AS `registrationId`,
    r.`registrationCode`,
    `r`.`name`    AS `Name`,
    (SELECT COUNT(*) FROM attenders a WHERE r.registrationId = a.registrationId AND a.attended = 1) AS attendedCount,
    IFNULL((SELECT SUM(`ch`.`amount`) FROM `charges` `ch` WHERE (`ch`.`registrationId` = `r`.`registrationId`)),0) AS `Fees`,
    IFNULL((SELECT SUM(`d`.`amount`) FROM `donations` `d` WHERE (`d`.`registrationId` = `r`.`registrationId`)),0) AS `Donations`,
    IFNULL((SELECT SUM(`c`.`amount`) FROM `credits` `c` WHERE (`c`.`registrationId` = `r`.`registrationId`)),0) AS `Credits`,
    IFNULL((SELECT SUM(`p`.`amount`) FROM `payments` `p` WHERE (`p`.`registrationId` = `r`.`registrationId`)),0) AS `Payments`
  FROM `registrations` r;


CREATE OR REPLACE VIEW balancesView AS
  SELECT
    b.year,
    b.registrationId,b.registrationCode, b.name,
    IF (b.attendedCount = 0, 0, 1) AS attended,
    FormatUSD(b.Fees,'') AS Fees,
    FormatUSD(b.Donations,'') AS Donations,
    FormatUSD(b.Credits,'') AS Credits,
    FormatUSD(b.Payments,'') AS Payments,
    FormatUSD(b.Fees + b.Donations - b.Credits - b.Payments, 'Paid in full') AS Balance
  FROM ledgerView b ORDER BY b.name;

-- SELECT * FROM balancesView ;
