-- Acts as a test for the ADODB_FORCE settings
DROP TABLE IF EXISTS adodb_force_insert;

-- adodb_force_insert is to test the ADODB_FORCE_INSERT
-- variables when no defaults are defined
CREATE TABLE adodb_force_insert (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	varchar_field VARCHAR(20),
	datetime_field DATETIME,
	date_field DATE,
	integer_field SMALLINT
	decimal_field DECIMAL(12.2),
	boolean_field SMALLINT,
	trigger_field SMALLINT
);
