CREATE OR REPLACE VIEW housingAssignmentsTextView AS
  SELECT r.registrationId,
                 a.attenderID AS attenderId,
    a.firstName, ScymNumberToWeekday(ha.day) AS 'dayOfWeek', ha.day AS 'day',
                 CASE ht.housingTypeCode
                 WHEN 'NONE' THEN 'Day visitor, no room assigned'
                 WHEN 'TENT' THEN 'Tenting, no room assigned'
                 ELSE CONCAT(hu.unitname,' - ',ht.housingTypeDescription)
                 END AS description
  FROM
    attenders a
    JOIN registrations r ON a.registrationId = r.registrationId
    JOIN housingassignments ha ON a.attenderID = ha.attenderId
    JOIN housingunits hu ON hu.housingUnitId = ha.housingUnitId
    JOIN housingtypes ht ON hu.housingTypeId = ht.housingTypeID;
