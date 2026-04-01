-- This file contains SQL commands to set up the database for unit tests
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (1, 'LINE 1',null,'2025-01-01',9001,1000.01,null,'',1);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (2, 'LINE 2',null,'2025-02-01',9002,1000.11,null,'',2);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (3, 'LINE 3',null,'2025-03-01',9003,1000.21,null,'',3);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (4, 'LINE 4',null,'2025-04-01',9004,1000.31,null,'',4);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (5, 'LINE 5',null,'2025-05-01',9005,1000.41,null,'',5);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (6, 'LINE 6',null,'2025-06-01',9006,1000.51,null,'',6);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (7, 'LINE 7',null,'2025-07-01',9007,1000.61,null,'',7);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (8, 'LINE 8',null,'2025-08-01',9008,1000.71,null,'',8);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (9, 'LINE 9',null,'1959-08-29',9009,1000.81,null,'',9);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (10, 'LINE 10',null,'2025-10-01',9010,1000.91,null,'',10);
INSERT INTO testtable_3 (id, varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values (11, 'LINE 11',null,'1725-11-01',-9011,-1000.11,null,'',11);
-- Insert a record to test foreign key constraints

-- PRINT Loading Foreign Constraint test data

INSERT INTO testtable_1 (varchar_field,	datetime_field,date_field, integer_field,decimal_field,boolean_field, empty_field,number_run_field) values ('LINE 2',null,'2025-02-01',9002,1000.11,null,'',2);

