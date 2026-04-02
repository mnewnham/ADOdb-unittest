-- Standard format for testing autoexecute based functions
-- used by all the test in /Helpers

DROP TABLE IF EXISTS autoexecute;

DROP SEQUENCE IF EXISTS autoexecute_seq;

DROP TRIGGER IF EXISTS autoexecute_t;

CREATE TABLE autoexecute (
	id INTEGER NOT NULL,
    varchar_field VARCHAR(20),
    date_field DATE,
    integer_field SMALLINT NOT NULL,
    decimal_field NUMBER(12,2),
    empty_field VARCHAR(240) DEFAULT '',
    number_run_field SMALLINT NOT NULL,
    PRIMARY KEY(id)
);

CREATE SEQUENCE autoexecute_seq
    INCREMENT BY 1
    START WITH 1;

-- This statement has an extraneous ; at end to force 
-- the procedure to be created in Oracle. It will be stripped
-- by the schema loader
CREATE OR REPLACE TRIGGER autoexecute_t BEFORE insert ON autoexecute FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select autoexecute_seq.nextval into :new.id from dual; END; ;
