DROP FUNCTION IF EXISTS FormatUSD;
CREATE FUNCTION FormatUSD(
  amount DECIMAL(10,2),
  zeroValue VARCHAR(10)
) RETURNS VARCHAR(20)
  RETURN IF(amount IS NULL OR amount = 0, zeroValue,
            CONCAT('$', FORMAT(amount,2)));
