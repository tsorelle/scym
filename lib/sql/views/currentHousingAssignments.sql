CREATE OR REPLACE VIEW currentHousingAssignments AS
SELECT ha.*,r.registrationId,a.FirstName,a.LastName
FROM housingassignments ha
  JOIN attenders a ON ha.attenderId = a.attenderID
  JOIN registrations r ON a.registrationId = r.registrationId
WHERE r.year  = currentYmYear();
