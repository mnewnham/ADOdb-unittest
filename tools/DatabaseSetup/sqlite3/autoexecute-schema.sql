-- Standard format for testing autoexecute based functions
-- used by all the test in /Helpers

DROP TABLE IF EXISTS testtable_1;

CREATE TABLE testtable_1 (
	id INTEGER,
	varchar_field VARCHAR(20),
	date_field DATE,
	integer_field INT(2) DEFAULT 0,
	decimal_field decimal(12.2) DEFAULT 0,
	empty_field VARCHAR(240) DEFAULT '',
	number_run_field INT(4) DEFAULT 0,
	PRIMARY KEY(id)
);
