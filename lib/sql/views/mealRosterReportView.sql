CREATE OR REPLACE VIEW mealRosterReportView AS
  SELECT r.year, m.mealTime,
    FormatName(a.firstName,a.middleName,a.lastName) AS 'name',
    a.attended,
    IF(a.glutenFree = 1,'Gluten-free','') AS glutenFree,
    IF(a.vegetarian = 1,'Vegetarian','') AS vegetarian
  FROM meals m
    JOIN attenders a ON a.attenderID = m.attenderID
    JOIN registrations r ON r.registrationId = a.registrationId;