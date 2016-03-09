drop view if exists FinanceSummary;
create view FinanceSummary as
select ra.registrationId, ra.Name, 
count(a.attenderId) as Attenders,
(select count(*) from attenders ac where ac.registrationId = ra.registrationId and generation = 1 and attended = 1) as Adults,
(select count(*) from attenders ac where ac.registrationId = ra.registrationId and generation = 2 and attended = 1) as Youth,
ifnull((select sum(ac.Meals) from AttenderCounts ac where ac.registrationId = ra.registrationId and ac.DayVisitor = 'Yes'),0)  as Meals,
ifnull((select sum(amount) div 6 from charges ch where ch.registrationId = ra.registrationId and feetypeid = 42),0) as LinenBags,
ifnull((select sum(Days) from AttenderCounts ac where ac.registrationId = ra.registrationId and DayVisitor = 'Yes' and generation = 'Adult'),0) as AdultDays,
ifnull((select sum(Days) from AttenderCounts ac where ac.registrationId = ra.registrationId and DayVisitor = 'Yes' and generation = 'Youth'),0) as YouthDays,
ifnull((select sum(DormNights) from AttenderCounts ac where ac.registrationId = ra.registrationId and generation = 'Adult'),0) as AdultDormNights,
ifnull((select sum(DormNights) from AttenderCounts ac where ac.registrationId = ra.registrationId and generation = 'Youth'),0) as YouthDormNights,
ifnull((select sum(CabinNights) from AttenderCounts ac where ac.registrationId = ra.registrationId and generation = 'Adult'),0) as AdultCabinNights,
ifnull((select sum(CabinNights) from AttenderCounts ac where ac.registrationId = ra.registrationId and generation = 'Youth'),0) as YouthCabinNights,
ra.Fees, ra.Donations, ra.Credits, ra.Payments,((ra.Fees + ra.Donations) - (ra.Credits + ra.Payments)) as Balance
from RegistrationAccounts ra
join attenders a on ra.registrationId = a.registrationId where a.attended = 1
group by ra.registrationId;

