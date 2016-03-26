CREATE OR REPLACE VIEW currentAttenderCountsView AS
  SELECT * FROM attenderCountsView v WHERE attended = 'Yes'
  AND v.year = currentYmYear();
