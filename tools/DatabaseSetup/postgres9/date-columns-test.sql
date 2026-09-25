-- Acts as a test for manipulation of date variables
DROP TABLE IF EXISTS date_columns_test;

CREATE TABLE date_columns_test (
	id SERIAL,
	datetime_field TIMESTAMP,
	date_field DATE,
	sqldate_test_field TIMESTAMP,
	offsetdate_test_field TIMESTAMP,
	PRIMARY KEY(id)
	
);
