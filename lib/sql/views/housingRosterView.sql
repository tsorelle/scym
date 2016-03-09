DROP VIEW IF EXISTS housingRosterView;
CREATE VIEW housingRosterView AS
  SELECT r.`year`,
    IFNULL(ha.day,0) AS dayNumber,
    IF(ha.day IS NULL, '', ScymNumberToWeekday(ha.day))  AS 'day',
    IF (hat.housingTypeCode IS NOT NULL, hat.housingTypeCode,
        IF (ht.housingTypeCode = 'NONE','DAY',
            IF (ht.housingTypeCode = 'TENT','TENT',
                'NOT ASSIGNED'))) AS assignedHousingType,
    a.registrationId, a.attenderId,
    ht.housingTypeDescription AS requested,
    CONCAT(TRIM(a.firstName),
           IF(a.middleName IS NULL OR a.middleName = '','',CONCAT(' ',TRIM(a.middleName))),' ',a.lastName) AS 'name',
    IF(ha.housingUnitId IS NULL,'[Not assigned]', hu.unitname) AS unit,
    IF(a.singleOccupant = 1,'Single','Double') AS occupancy,
    IF(ha.confirmed = 1, 'Yes', 'No') AS confirmed,
    IF (a.attended = 1, 'Yes', 'No') AS arrived,
    IF(ha.note IS NULL,'',ha.note) AS note
  FROM attenders a
    JOIN housingtypes ht ON ht.housingTypeId = a.`housingTypeId`
    JOIN registrations r ON a.`registrationId` = r.`registrationId`
    LEFT OUTER JOIN housingassignments ha ON ha.`attenderId` = a.`attenderID`
    LEFT OUTER JOIN housingunits hu ON ha.`housingUnitId` = hu.`housingUnitId`
    LEFT OUTER JOIN housingtypes hat ON hat.housingTypeId = hu.housingTypeId
  ORDER BY ha.day, a.lastName, a.firstName;
