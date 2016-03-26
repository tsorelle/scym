DROP FUNCTION IF EXISTS  currentYmYear;

DELIMITER $$
CREATE FUNCTION currentYmYear()
RETURNS INT
BEGIN
  SET @result = 0;
  SELECT a.year FROM annualsessions a
    WHERE a.start >= DATE_ADD(CURRENT_DATE(), INTERVAL -270 DAY)
    AND a.end <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY)
    INTO @result;

  RETURN @result;
END;
$$
DELIMITER ;