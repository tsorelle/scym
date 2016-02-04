DROP VIEW IF EXISTS housingRequestCountsView;
DROP VIEW IF EXISTS housingRequestCountsReportView;
CREATE VIEW housingRequestCountsReportView AS
  SELECT
    ht.housingTypeId, ht.housingTypeDescription,
    COUNT(a.attenderId) AS requested,
    IFNULL(IF(ht.`housingTypeID` IN (1,11), '', (SELECT SUM(capacity) FROM housingunits WHERE housingtypeid = ht.housingTypeId)),0) AS available,
    IF(ht.`housingTypeID` IN (1,11), '', (SELECT COUNT(DISTINCT attenderId) FROM currentHousingAssignments ha WHERE ha.housingTypeId = ht.`housingTypeID`)) AS assigned
  FROM
    housingtypes ht
    LEFT OUTER JOIN currentAttenders a ON a.housingTypeId = ht.housingTypeId
  GROUP BY  ht.housingTypeId



