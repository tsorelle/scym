DROP FUNCTION IF EXISTS ScymMealTime;
CREATE FUNCTION ScymMealTime(t INT)
  RETURNS VARCHAR(20)
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
          WHEN '1' THEN 'Breakfast'
          WHEN '2' THEN 'Lunch'
          WHEN '3' THEN 'Dinner'
          ELSE 'Error' END));
