drop view if exists RegistrationAccounts;
create view RegistrationAccounts as
select r.registrationId, r.addressName as Name,
ifnull((select sum(amount) from charges ch where ch.registrationid = r.registrationid),0) as Fees,
ifnull((select sum(amount) from donations d where d.registrationid = r.registrationid),0) as Donations,
ifnull((select sum(amount) from credits c where c.registrationid = r.registrationid),0) as Credits,
ifnull((select sum(amount) from payments p where p.registrationid = r.registrationid),0) as Payments
from registrations r;
