-- Convert arrival/departure time value (tens = day, ones = Morning|Noon|Evening) to string
create function ScymTime(t int)
returns varchar(20)
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
