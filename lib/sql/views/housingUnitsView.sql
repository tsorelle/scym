DROP VIEW IF EXISTS housingUnitsView;
CREATE VIEW housingUnitsView AS
SELECT
  u.housingUnitId,
  u.unitname,
  CONCAT(u.unitname,' (',u.capacity,')') AS description,
  ht.housingTypeID AS housingTypeId,
  ht.housingTypeDescription  AS housingTypeName,
  ht.category AS housingCategoryId,
  CONVERT(
      (CASE ht.category WHEN 1 THEN 'Dorm' WHEN 2 THEN 'Cabin' WHEN 3 THEN 'Motel' ELSE ''  END)
      USING utf8) AS categoryName,
  u.capacity,
  u.active
FROM  housingunits u
JOIN  housingtypes ht  on u.housingTypeId = ht.housingTypeID;
