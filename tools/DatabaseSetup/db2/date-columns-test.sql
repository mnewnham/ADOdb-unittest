-- Acts as a test for manipulation of date variables
DROP TABLE IF EXISTS date_columns_test;

CREATE TABLE date_columns_test (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	datetime_field DATETIME,
	date_field DATE,
	sqldate_test_field DATETIME,
	offsetdate_test_field DATETIME
	
);
