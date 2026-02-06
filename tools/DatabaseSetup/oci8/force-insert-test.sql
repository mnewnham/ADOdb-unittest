-- Acts as a test for the ADODB_FORCE settings
DROP TABLE IF EXISTS adodb_force_insert;
DROP SEQUENCE IF EXISTS adodb_force_insert_seq;
DROP TRIGGER IF EXISTS adodb_force_insert_t;

-- adodb_force_insert is to test the ADODB_FORCE_INSERT
-- variables when no defaults are define
CREATE TABLE adodb_force_insert (
	id INTEGER NOT NULL,
	varchar_field VARCHAR(20),
	datetime_field DATETIME,
	date_field DATE,
	integer_field SMALLINT,
	decimal_field NUMBER(12.2),
	boolean_field NUMBER(1,0),
	trigger_field SMALLINT
);

-- Creates an auto-increment column
CREATE SEQUENCE adodb_force_insert_seq
    INCREMENT BY 1
    START WITH 1;

CREATE OR REPLACE TRIGGER adodb_force_insert_t BEFORE insert ON adodb_force_insert FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select adodb_force_insert_seq.nextval into :new.id from dual; END; ;