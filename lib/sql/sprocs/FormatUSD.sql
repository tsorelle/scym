DROP FUNCTION IF EXISTS FormatUSD;
CREATE FUNCTION FormatUSD(
  amount DECIMAL(10,2),
  zeroValue VARCHAR(20)
) RETURNS VARCHAR(20)
  RETURN IF(amount IS NULL OR amount = 0, zeroValue,
            IF(amount < 0,CONCAT('($', FORMAT(ABS(amount),2),')'),
               CONCAT('$', FORMAT(amount,2))));

