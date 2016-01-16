-- CREATE VIEW youthView AS
ALTER VIEW youthView AS
SELECT
  r.year, yo.attenderId, yo.youthId, r.registrationId,
  a.firstName,a.lastName,
  FormatName(a.firstName, a.middleName, a.lastName) AS fullName,
  IFNULL(a.affiliationCode,'') AS affiliationCode,
  IFNULL(ac.affiliation,'') AS meeting,
  a.generationId,
  g.generationName,
  a.arrivalTime,
  a.departureTime,
  ScymTime(a.arrivalTime) AS arrivalTimeText,
  ScymTime(a.departureTime) AS departureTimeText,
  IF(yo.dateOfBirth IS NULL,'',DATE_FORMAT(yo.dateOfBirth,'%Y-%m-%d')) AS dateOfBirth,
  IF(yo.dateOfBirth IS NULL,'',DATEDIFF(CURRENT_DATE,yo.dateOfBirth) DIV 365) AS age,
  yo.gradeLevel,
  r.name AS registrationName,
  yo.sponsor,
  IFNULL(sp.description,'') AS specialNeeds,
  IFNULL(ag.groupName,'(unknown)') AS ageGroup,
  IFNULL(ag.cutoffAge,0) AS ageGroupCutoff,
  IFNULL(ag.ageGroupId,0) AS ageGroupId,
  (CASE a.vegetarian
   WHEN 1 THEN
     CASE glutenFree
     WHEN 1 THEN 'Vegetarian and Gluten free'
     ELSE 'Vegetarian'
     END
   ELSE
     CASE glutenFree
     WHEN 1 THEN 'Gluten free'
     ELSE ''
     END
   END ) AS dietPreference,
  yo.notes AS youthNotes,
  a.notes AS attenderNotes,
  IFNULL(yo.formsSubmitted,0) AS formsSubmitted,
  IF((yo.notes IS NOT NULL AND yo.notes <> '') OR (a.notes IS NOT NULL AND a.notes <> ''),'See notes',NULL) AS hasNotes
FROM youths yo
  JOIN attenders a ON a.attenderID = yo.attenderId
  JOIN generations g ON a.generationId = g.generationId
  JOIN registrations r ON a.registrationId = r.registrationId
  LEFT OUTER JOIN agegroups ag ON yo.ageGroupId = ag.ageGroupId
  LEFT OUTER JOIN specialneedstypes sp ON a.specialNeedsTypeId = sp.specialNeedsTypeID
  LEFT OUTER JOIN affiliationcodes ac ON ac.affiliationCode = a.affiliationCode


