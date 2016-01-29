DROP VIEW IF EXISTS attenderReport;

create view attenderReport as
select
attenderId, registrationId, affiliationId,
firstName, middleName, lastName, attended, feeCredit, arrivalTime, departureTime, generation as GenerationId,
if(affiliationId is null,0,1) as ScymMembership,
concat(lastName,', ',firstName,if(middleName is null, '',concat(' ',middleName))) as AttenderName,
if(m.meetingName is null,if(otherAffiliation is null,'(unknown)',otherAffiliation),m.meetingName) as Affiliation,
if(firstTimer = 1,'Yes','No') as FirstTimeAttender,
ScymTime(arrivalTime) as Arrival,
ScymTime(departureTime) as Departure,
if(attended = 1,'Yes','No') as CheckedIn,
(case generation
	when 1 then 'Adult'
        when 2 then 'Youth'
        when 4 then 'Baby'
	else '' end) as Generation,
(case feeCredit
	when 2 then 'Teacher'
        when 4 then 'Staff'
	else '' end) as Role
from attenders a
left outer join meetinginfo m on a.affiliationId = m.meetingInfoId;


/*
COLUMNS:
 -- for filter and sort
affiliationId,
firstName,
middleName,
lastName,
attended,
feeCredit,
arrivalTime,
departureTime,
GenerationId,
ScymMembership,

-- for display
attenderId,
registrationId,
AttenderName,
Affiliation,
FirstTimeAttender,
Arrival,
Departure,
CheckedIn,
Generation,
Role
