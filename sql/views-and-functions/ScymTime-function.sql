DELIMITER $$

USE `scym_org`$$

DROP FUNCTION IF EXISTS `ScymTime`$$

CREATE DEFINER=`scymuser`@`%` FUNCTION `ScymTime`(t INT) RETURNS VARCHAR(20) CHARSET latin1
RETURN
   IF (t IS NULL OR t = 0, '' ,
   CONCAT(
	CASE (t DIV 10)
	  WHEN '4' THEN 'Thursday'
	  WHEN '5' THEN 'Friday'
          WHEN '6' THEN 'Saturday'
          WHEN '7' THEN 'Sunday'
          ELSE 'Error' END,' ',
	CASE (t MOD 10)
	  WHEN '1' THEN 'Morning'
          WHEN '2' THEN 'Noon'
	  WHEN '3' THEN 'Evening'
          ELSE 'Error' END))$$

DELIMITER ;