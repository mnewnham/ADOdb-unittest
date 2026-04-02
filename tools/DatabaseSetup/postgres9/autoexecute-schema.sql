-- Standard format for testing autoexecute based functions
-- used by all the test in /Helpers
DROP TABLE IF EXISTS autoexecute;

CREATE TABLE autoexecute (
	id SERIAL PRIMARY KEY,
	varchar_field VARCHAR(20),
	date_field DATE,
	integer_field SMALLINT DEFAULT 0,
	decimal_field decimal(12,2) DEFAULT 0.0,
	empty_field VARCHAR(240) DEFAULT '',
	number_run_field BIGINT DEFAULT 0
);
