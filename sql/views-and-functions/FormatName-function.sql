DELIMITER $$

USE `scym_org`$$

DROP FUNCTION IF EXISTS `FormatName`$$

CREATE DEFINER=`scymuser`@`%` FUNCTION `FormatName`(
firstName VARCHAR(200),
middleName VARCHAR(200),
lastName VARCHAR(200)
) RETURNS VARCHAR(500) CHARSET latin1
RETURN CONCAT(TRIM(firstName), IF(middleName IS NULL OR middleName = '','',CONCAT(' ',TRIM(middleName))),' ',lastName)$$

DELIMITER ;