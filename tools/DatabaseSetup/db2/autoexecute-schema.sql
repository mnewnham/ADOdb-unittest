-- Standard format for testing autoexecute based functions
-- used by all the test in /Helpers

DROP TABLE IF EXISTS AUTOEXECUTE;

CREATE TABLE AUTOEXECUTE (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	varchar_field VARCHAR(20),
	date_field DATE DEFAULT CURRENT DATE,
	integer_field SMALLINT NOT NULL DEFAULT 0,
	decimal_field DECIMAL(12,2) DEFAULT 0,
	empty_field VARCHAR(240) DEFAULT '',
	number_run_field INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);
