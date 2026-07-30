-- Standard format for testing autoexecute based functions
-- used by all the test in /Helpers
DROP TABLE IF EXISTS autoexecute;

CREATE TABLE autoexecute (
	id BIGINT IDENTITY(1,1),
	varchar_field VARCHAR(20),
	date_field DATE,
	integer_field INTEGER DEFAULT 0,
	decimal_field DECIMAL(12,2) DEFAULT 0,
	empty_field VARCHAR(240) DEFAULT '',
	number_run_field BIGINT DEFAULT 0,
	decimal_eval_field decimal(12,4) DEFAULT 5,
	varchar_eval_field VARCHAR(20),
	tinyint_field TINYINT DEFAULT 0,
	smallint_field SMALLINT DEFAULT 0,
	PRIMARY KEY(id)
);
