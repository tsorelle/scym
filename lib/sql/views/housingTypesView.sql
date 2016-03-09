DROP VIEW IF EXISTS  housingTypesView;
CREATE VIEW housingTypesView AS
SELECT
  housingTypeID AS housingTypeId,
  housingTypeCode,
  housingTypeDescription,
  category,
  CONVERT(
      (CASE category WHEN 1 THEN 'Dorm' WHEN 2 THEN 'Cabin' WHEN 3 THEN 'Motel' ELSE ''  END)
      USING utf8) AS categoryName,
  active
FROM housingtypes;
