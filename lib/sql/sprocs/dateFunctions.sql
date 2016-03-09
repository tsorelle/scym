DROP FUNCTION IF EXISTS DisplayDate;
CREATE FUNCTION DisplayDate(
  dateValue DATETIME,
  nullValue VARCHAR(10)
) RETURNS VARCHAR(20)
  RETURN IF(dateValue IS NULL,nullValue,DATE_FORMAT(dateValue,'%Y-%m-%d'));

DROP FUNCTION IF EXISTS DisplayMediumDate;
CREATE FUNCTION DisplayMediumDate(
  dateValue DATETIME,
  nullValue VARCHAR(10)
) RETURNS VARCHAR(20)
  RETURN IF(dateValue IS NULL,nullValue,DATE_FORMAT(dateValue,'%b %d, %Y'));

DROP FUNCTION IF EXISTS DisplayLongDate;
CREATE FUNCTION DisplayLongDate(
  dateValue DATETIME,
  nullValue VARCHAR(10)
) RETURNS VARCHAR(20)
  RETURN IF(dateValue IS NULL,nullValue,DATE_FORMAT(dateValue,'%M %e, %Y'));

/*
SELECT    DisplayDate(CURRENT_DATE(),'') AS shortdate,
          DisplayMediumDate(CURRENT_DATE(),'') AS  mediumdate,
          DisplayLongDate(CURRENT_DATE(),'') AS longdate;
*/