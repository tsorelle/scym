CREATE OR REPLACE VIEW registrarsReportView AS
  SELECT 'Registrations' AS itemName,
         (SELECT COUNT(*) FROM currentRegistrationsView) AS 'registered',
         (SELECT COUNT(*) FROM currentRegistrationsView WHERE attended='Yes') AS 'attended'
  UNION
  SELECT 'Attenders' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView) AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE CheckedIn='Yes') AS 'attended'
  UNION
  SELECT 'Adults' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId = 1 ) AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId = 1 AND CheckedIn='Yes') AS 'attended'
  UNION
  SELECT 'Youth' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId IN (2,3,4) ) AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId IN (2,3,4) AND CheckedIn='Yes') AS 'attended'
  UNION
  SELECT 'Little friends' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId = 5)  AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE generationId = 5 AND CheckedIn='Yes') AS 'attended'
  UNION
  SELECT 'First timers' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE firstTimer = 1)  AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE firstTimer = 1 AND CheckedIn='Yes') AS 'attended'
  UNION
  SELECT 'Day attenders' AS itemName,
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE housingPreference = 1)  AS 'registered',
         (SELECT COUNT(*) FROM currentAttendersReportView WHERE housingPreference = 1 AND CheckedIn='Yes') AS 'attended'
;


-- select * from registrarsReportView;