
-- CREATE
ALTER
VIEW housingUnitsView AS
SELECT
  ht.housingTypeID AS housingTypeId,
  u.housingUnitId,
  u.unitname,
  ht.housingTypeDescription  AS housingTypeName,
  ht.category AS housingCategoryId,
  CONVERT(
      (CASE ht.category WHEN 1 THEN 'Dorm' WHEN 2 THEN 'Cabin' WHEN 3 THEN 'Motel' ELSE ''  END)
      USING utf8) AS categoryName,
  u.capacity
FROM housingunits u JOIN housingtypes ht ON u.housingTypeId = ht.housingTypeId
WHERE u.active = 1
GROUP BY u.housingUnitId