-- Creates Simple stored procedures for testing Meta and Core
-- Contains custom unittest SQL instructions
DROP PROCEDURE IF EXISTS sp_recordset_test;

-- A Simple Procedure that returns a recordset
-- READ <<
DELIMITER //
CREATE PROCEDURE sp_recordset_test(IN filter_number INT)
BEGIN
    SELECT * FROM testtable_3 WHERE number_run_field < filter_number;
END//
DELIMITER ;
-- <<