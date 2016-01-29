DROP VIEW IF EXISTS housingAssignmentsTextView;
CREATE VIEW housingAssignmentsTextView AS
  SELECT r.registrationId, a.attenderID, a.firstName, ScymNumberToWeekday(ha.day) AS DAY,
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
