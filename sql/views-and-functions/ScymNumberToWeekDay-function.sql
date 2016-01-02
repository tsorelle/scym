DELIMITER $$

USE `scym_org`$$

DROP FUNCTION IF EXISTS `ScymNumberToWeekday`$$

CREATE DEFINER=`scymuser`@`%` FUNCTION `ScymNumberToWeekday`(number INT) RETURNS VARCHAR(10) CHARSET latin1
RETURN
	(CASE number
	   WHEN 1 THEN 'Monday'
	   WHEN 2 THEN 'Tuesday'
           WHEN 3 THEN 'Wednesday'
	   WHEN 4 THEN 'Thursday'
           WHEN 5 THEN 'Friday'
           WHEN 6 THEN 'Saturday'
	   WHEN 7 THEN 'Sunday'
	   ELSE ''
	END)$$

DELIMITER ;