-- Acts as a test for manipulation of date variables
DROP TABLE IF EXISTS date_columns_test;
DROP SEQUENCE IF EXISTS date_columns_test_seq;

CREATE TABLE date_columns_test (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	datetime_field DATETIME,
	date_field DATE,
	sqldate_test_field DATETIME,
	offsetdate_test_field DATETIME
	
);

CREATE SEQUENCE date_columns_test_seq
    INCREMENT BY 1
    START WITH 1;

-- This statement has an extraneous ; at end to force 
-- the procedure to be created in Oracle. It will be stripped
-- by the schema loader
CREATE OR REPLACE TRIGGER date_columns_test_t BEFORE insert ON date_columns_test FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select date_columns_test_seq.nextval into :new.id from dual; END; ;

