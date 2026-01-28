-- Acts as a test for the ADODB_FORCE settings
DROP TABLE IF EXISTS adodb_force_insert;

-- adodb_force_insert is to test the ADODB_FORCE_INSERT
-- variables when no defaults are define
CREATE TABLE adodb_force_insert (
	id INT IDENTITY(1,1) PRIMARY KEY,
	varchar_field VARCHAR(20),
	datetime_field DATETIME,
	date_field DATE,
	integer_field SMALLINT,
	decimal_field DECIMAL(12,2),
	boolean_field BIT,
	trigger_field SMALLINT
);