drop table scymaffiliations;

create view scymaffiliations as
select meetingInfoId as affiliationId, affiliationCode, meetingname as affiliation
from meetinginfo;

select * from scymaffiliations;