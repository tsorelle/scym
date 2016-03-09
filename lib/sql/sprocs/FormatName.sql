-- Return full name from parts
CREATE FUNCTION FormatName(
firstName varchar(200),
middleName varchar(200),
lastName varchar(200)
) RETURNS varchar(500) 
  return concat(trim(firstName), if(middleName is null or middleName = '','',concat(' ',trim(middleName))),' ',lastName)



