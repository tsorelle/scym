CREATE OR REPLACE VIEW currentAttenderCountsView AS
  SELECT * FROM attenderCountsView v WHERE attended = 'Yes' AND v.year IN
    (SELECT a.year FROM annualsessions a
    WHERE a.start >= CURRENT_DATE() AND a.end <= DATE_ADD(CURRENT_DATE(), INTERVAL 90 DAY) );