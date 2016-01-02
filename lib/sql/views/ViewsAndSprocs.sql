DROP FUNCTION IF EXISTS FormatName
CREATE FUNCTION FormatName(
firstName varchar(200),
middleName varchar(200),
lastName varchar(200)
) RETURNS varchar(500)
DETERMINISTIC
  return concat(trim(firstName), if(middleName is null or middleName = '','',concat(' ',trim(middleName))),' ',lastName)

DROP FUNCTION IF EXISTS  ScymNumberToWeekday
create function ScymNumberToWeekday(number int)
returns varchar(10)
DETERMINISTIC
  return
	(case number
	   when 1 then 'Monday'
	   when 2 then 'Tuesday'
           when 3 then 'Wednesday'
	   when 4 then 'Thursday'
           when 5 then 'Friday'
           when 6 then 'Saturday'
	   when 7 then 'Sunday'
	   else ''
	end)

DROP FUNCTION IF EXISTS ScymTime
create function ScymTime(t int)
returns varchar(20)
DETERMINISTIC
RETURN
   if (t is null or t = 0, '' ,
   concat(
	case (t div 10)
	  when '4' then 'Thursday'
	  when '5' then 'Friday'
          when '6' then 'Saturday'
          when '7' then 'Sunday'
          else 'Error' end,' ',
	case (t mod 10)
	  when '1' then 'Morning'
          when '2' then 'Noon'
	  when '3' then 'Evening'
          else 'Error' end));


DROP VIEW IF EXISTS HousingCountsDetail;
create view HousingCountsDetail as
select a.attenderId, a.lastName, a.firstName, a.generation, h.unit,  ht.category,
if (ht.category=1,'Dorm','Cabin/Motel') as CampCategory,
count(h.housingAssignmentId) as nights -- h.day,
from housingassignments h
join attenders a on h.attenderId = a.attenderId
join housingunits hu on h.unit = hu.unitName
join housingtypes ht on hu.housingTypeCode = ht.housingTypeCode
where generation < 3 and attended = 1
group by category, firstname, lastname,h.unit
order by category,nights;

DROP VIEW IF EXISTS AttenderCounts;

create view AttenderCounts as
SELECT a.attenderId, registrationId, 
FormatName(a.firstName, a.middleName, a.lastName) as Name,
ScymTime(arrivalTime) as Arrival, 
ScymTime(departureTime) as Departure, 
(case a.generation
   when 1 then 'Adult'
   when 2 then 'Youth'
   when 3 then 'Baby'
   else '?'
end) as Generation,
(case feecredit 
   when 2 then 'Teacher'
   when 3 then 'Guest'
   when 4 then 'Staff'
   else ''
end) as Role,
(departureTime div 10 - arrivalTime div 10 + 1) as Days,
(departureTime div 10 - arrivalTime div 10) as Nights,
ifnull((select sum(nights) from HousingCountsDetail where attenderid = a.attenderid and category = 1),0) as DormNights,
ifnull((select sum(nights) from HousingCountsDetail where attenderid = a.attenderid and category > 1), 0) as CabinNights,
(select count(*) from meals where attenderid = a.attenderid) as Meals,
if(a.housing = 1,'Yes','No') as DayVisitor
FROM attenders a
WHERE attended = 1
order by a.lastName, a.firstName;

drop view if exists RegistrationAccounts;
create view RegistrationAccounts as
select r.registrationId, r.addressName as Name,
ifnull((select sum(amount) from charges ch where ch.registrationid = r.registrationid),0) as Fees,
ifnull((select sum(amount) from donations d where d.registrationid = r.registrationid),0) as Donations,
ifnull((select sum(amount) from credits c where c.registrationid = r.registrationid),0) as Credits,
ifnull((select sum(amount) from payments p where p.registrationid = r.registrationid),0) as Payments
from registrations r;

drop view if exists FinanceSummary;
create view FinanceSummary as
select ra.registrationId, ra.Name, 
count(a.attenderId) as Attenders,
(select count(*) from attenders ac where ac.registrationId = ra.registrationId and generation = 1 and attended = 1) as Adults,
(select count(*) from attenders ac where ac.registrationId = ra.registrationId and generation = 2 and attended = 1) as Youth,
ifnull((select sum(ac.Meals) from AttenderCounts ac where ac.registrationId = ra.registrationId and ac.DayVisitor = 'Yes'),0)  as Meals,
ifnull((select (sum(amount) div ft.unitAmount) from charges c join feetypes ft on ft.feeTypeID = c.feeTypeID where c.registrationId = ra.registrationId and feeCode = 'LINEN'),0) as Linens, 
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

drop view if exists LedgerDetail;
create view LedgerDetail as
select ch.RegistrationId, 'Charge' as EntryType, 'debit' as TransactionType, 1 as ItemGroup, ft.description as Type, ft.feeTypeID as ItemTypeId, Amount
from charges ch
join feetypes ft on  ft.feeTypeID =  ch.feeTypeID
union
select d.RegistrationId, 'Donation' as EntryType, 'debit' as TransactionType, 2 as ItemGroup, dt.description as Type, dt.donationTypeId as ItemTypeId, Amount
from donations d
join donationtypes dt on dt.donationTypeId = d.donationTypeId
union 
select c.RegistrationId, 'Credit' as EntryType, 'credit' as TransactionType, 3 as ItemGroup, ct.creditType as Type, ct.creditTypeId as ItemTypeId, Amount
from credits c
join credittypes ct on ct.creditTypeId = c.creditTypeId
union 
select p.RegistrationId, 'Payment' as EntryType, 'credit' as TransactionType, 4 as ItemGroup, 'Payment' as Type, 1 as ItemTypeId, Amount
from payments p
join attenders a on a.registrationid = p.registrationid
order by ItemGroup, ItemTypeId;


drop view if exists RegistrationBalances; 
create view RegistrationBalances as 
select registrationId,Name,Fees,Donations,Credits, Payments, (Fees + Donations - Credits - Payments) as Balance from RegistrationAccounts
where registrationId in (select r.registrationId from registrations r join attenders a on r.registrationid = a.registrationid where a.attended = 1);

drop view if exists Ledger;
create view Ledger as
Select distinct l.* from LedgerDetail l
join attenders a on l.registrationId = a.registrationId and a.attended = 1;

drop view if exists EventReceiptsReport;
create view EventReceiptsReport as
select ItemGroup, Type as EntryType, sum(Amount) as Total, count(*) as Number
from Ledger
where registrationId in (select r.registrationId from registrations r join attenders a on r.registrationid = a.registrationid where a.attended = 1)
group by ItemGroup,Type 
union
select ItemGroup, concat('Total ',EntryType,'s') as EntryType, sum(Amount) as Total, count(*) as Number
from Ledger
group by ItemGroup 
union
select 5 as ItemGroup, 'Total debit transactions' as EntryType, 
	((select sum(amount) from Ledger where transactionType = 'debit')) as Amount, (select count(*) from Ledger where transactionType = 'debit') as Number
union
select 6 as ItemGroup, 'Total credit transactions' as EntryType, 
	((select sum(amount) from Ledger where transactionType = 'credit')) as Amount,(select count(*) from Ledger where transactionType = 'credit') as Number
union
select 7 as ItemGroup, 'Amount still due' as EntryType, 
	((select sum(amount) from Ledger where transactionType = 'debit') -
	(select sum(amount) from Ledger where transactionType = 'credit')) as Amount, 
	(select count(*) from RegistrationBalances where Balance < 0.1) as Number;

drop view if exists FinanceCountSummary;
create view FinanceCountSummary as
select (4 - Nights) as LineOrder, concat(CampCategory,' ',Nights,' night ') as Item, count(*) as Count
from HousingCountsDetail
group by Nights, CampCategory
union
select 4 as LineOrder, 'Day visitors - Adult' as Item,
(select sum(Days) from AttenderCounts where Generation = 'Adult' and DayVisitor = 'Yes') as Count
union
select 5 as LineOrder, 'Day visitors - Youth' as Item,
(select sum(Days) from AttenderCounts where Generation = 'Youth' and DayVisitor = 'Yes') as Count
union
select 6 as LineOrder,  'Extra Meals' as Item,
(select count(*) from meals m join attenders a on m.attenderId = a.attenderId where a.housing = 1 and a.attended = 1) as Count
union
select 7 as LineOrder, 'Linen Bags' as Item,
(select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'LINEN') as Count
order by LineOrder;

drop view if exists FinanceCountSummaryByCharges;
create view FinanceCountSummaryByCharges as
select 'Cabin/Motel 3 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT3_ROOM'),0) as Count
union
select 'Dorm 3 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT3_DORM'),0) as Count
union
select 'Cabin/Motel 2 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT2_ROOM'),0) as Count
union
select 'Dorm 2 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT2_DORM'),0) as Count
union
select 'Cabin/Motel 1 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT1_ROOM'),0) as Count
union
select 'Dorm 2 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'ADULT1_DORM'),0) as Count
union
select 'Youth 3 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'YOUTH3'),0) as Count
union
select 'Youth 2 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'YOUTH2'),0) as Count
union
select 'Youth 1 night' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'YOUTH1'),0) as Count
union
select 'Day visitors - Adult' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'DAY'),0) as Count
union
select 'Day visitors - Youth' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'YOUTHDAY'),0) as Count
union
select 'Extra Meals' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'MEAL'),0) as Count
union
select 'Linen Bags' as Item,
ifnull((select (sum(amount) div ft.unitAmount) from charges c
join feetypes ft on ft.feeTypeID = c.feeTypeID
where feeCode = 'LINEN'),0) as Count;

