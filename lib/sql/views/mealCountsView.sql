DROP VIEW IF EXISTS mealCountsView;
CREATE VIEW mealCountsView AS
  -- all regular diet
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 0 AS diettype, 0 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE  (a.`glutenFree` IS NULL OR a.`glutenFree` = 0) AND (a.`vegetarian` IS NULL OR a.`vegetarian` = 0)
  GROUP BY m.`mealTime`

  -- attended regular diet
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 0 AS diettype, 1 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE  (a.`glutenFree` IS NULL OR a.`glutenFree` = 0) AND (a.`vegetarian` IS NULL OR a.`vegetarian` = 0) AND a.`attended` = 1
  GROUP BY m.`mealTime`

  -- all veggy
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 1 AS diettype, 0 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NULL OR a.`glutenFree` = 0) AND (a.`vegetarian` IS NOT NULL AND a.`vegetarian` = 1)
  GROUP BY m.`mealTime`

  -- attended veggy
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 1 AS diettype, 1 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NULL OR a.`glutenFree` = 0) AND (a.`vegetarian` IS NOT NULL AND a.`vegetarian` = 1)  AND attended = 1
  GROUP BY m.`mealTime`

  -- all gluten free
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 2 AS diettype, 0 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NOT NULL AND a.`glutenFree` = 1) AND (a.`vegetarian` IS NULL OR a.`vegetarian` = 0)
  GROUP BY m.`mealTime`

  -- attended gluten free
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 2 AS diettype, 1 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NOT NULL AND a.`glutenFree` = 1) AND (a.`vegetarian` IS NULL OR a.`vegetarian` = 0) AND a.`attended` = 1
  GROUP BY m.`mealTime`

  -- all both
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 3 AS diettype, 0 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NOT NULL AND a.`glutenFree` = 1) AND (a.`vegetarian` IS NOT NULL AND a.`vegetarian` = 1)
  GROUP BY m.`mealTime`

  -- attended both
  UNION
  SELECT m.`mealTime`, ScymMealTime(m.`mealTime`) AS mealName, 3 AS diettype, 1 AS attendersOnly, COUNT(*) AS COUNT
  FROM meals m
    JOIN currentAttenders a ON a.`attenderID` = m.`attenderID`
  WHERE (a.`glutenFree` IS NOT NULL AND a.`glutenFree` = 1) AND (a.`vegetarian` IS NOT NULL AND a.`vegetarian` = 1) AND a.`attended` = 1
  GROUP BY m.`mealTime`;
