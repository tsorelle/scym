CREATE FUNCTION YesOrNo(number TINYINT)
  RETURNS VARCHAR(3)
  RETURN (
    CASE number
    WHEN 1 THEN 'Yes'
    ELSE 'No'
    END
  )