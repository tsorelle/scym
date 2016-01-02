DROP VIEW IF EXISTS housingcountsdetail;
create view housingcountsdetail as 
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

