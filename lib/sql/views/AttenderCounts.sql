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
ifnull((select sum(nights) from housingcountsdetail where attenderid = a.attenderid and category = 1),0) as DormNights,
ifnull((select sum(nights) from housingcountsdetail where attenderid = a.attenderid and category > 1), 0) as CabinNights,
(select count(*) from meals where attenderid = a.attenderid) as Meals,
if(a.housing = 1,'Yes','No') as DayVisitor
FROM attenders a
WHERE attended = 1
order by a.lastName, a.firstName;

