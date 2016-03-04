CREATE OR REPLACE VIEW nameTagsDownloadView AS
  SELECT
    FormatName(a.firstName,a.middleName,a.lastName) AS 'Name',
    IF(m.affiliationCode IS NULL,a.otherAffiliation,m.meetingName) AS Affiliation,
    IF(a.firstTimer IS NULL,'','first timer') AS firstTimer
  FROM currentAttenders a
    LEFT OUTER JOIN meetings m ON m.affiliationCode = a.affiliationCode
  ORDER BY lastName,firstName,middleName;

SELECT * FROM nameTagsDownloadView;