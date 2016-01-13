DROP VIEW IF EXISTS attendersView;
CREATE VIEW attendersView AS
  SELECT
    a.attenderID AS attenderId,
    r.registrationId,
    r.year,
    YesOrNo(attended) AS arrived,
    FormatName(a.firstName,a.middleName,a.lastName) AS 'name',
    a.generationId,
    g.generationName,
    IFNULL(ag.groupName,'') AS ageGroup,
    (CASE vegetarian
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
    IFNULL(sp.description,'') AS specialNeeds,
    YesOrNo(a.firstTimer) AS firstTimer,
    IF(a.affiliationCode IS NULL,'',
       (
         CASE ac.affiliationCode
         WHEN 'NONE' THEN ''
         ELSE ac.affiliation
         END
       )) AS meeting,
    a.notes AS note,
    YesOrNo(a.linens) AS linens
  FROM attenders a
    JOIN registrations r ON a.registrationId = r.registrationId
    JOIN generations g ON a.generationId = g.generationId
    LEFT OUTER JOIN specialneedstypes sp ON a.specialNeedsTypeId = sp.specialNeedsTypeID
    LEFT OUTER JOIN affiliationcodes ac ON a.affiliationCode= ac.affiliationCode
    LEFT OUTER JOIN youths yo ON yo.attenderId = a.attenderId
    LEFT OUTER JOIN agegroups ag ON yo.ageGroupId = ag.ageGroupId;
