-- Convert number to weekday string
create function ScymNumberToWeekday(number int)
returns varchar(10)
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
