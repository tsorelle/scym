-- select 
-- truncate((sum(c.amount) / ft.unitAmount),0) as ExtraMeals
-- from charges c
-- join  feetypes ft on c.feeTypeId = ft.feeTypeId
-- where ft.feeCode = 'MEAL'
-- -- group by ft.feeCode

-- SELECT c.amount from charges c where feeTypeId = 32

create view registrationsAttended as
select r.* 
from registrations r
join attenders a on a.registrationId = r.registrationId
where a.attended = 1 and r.active = 1
group by r.registrationId


