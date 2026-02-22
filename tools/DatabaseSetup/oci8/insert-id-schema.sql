-- Inserr Id test suite for Oracle driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

DROP SEQUENCE IF EXISTS SEQ_INSERT_AUTO;

DROP TRIGGER IF EXISTS insert_auto_trigger;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id INTEGER NOT NULL,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY (id)
);

CREATE SEQUENCE SEQ_INSERT_AUTO
    INCREMENT BY 1
    START WITH 1;

CREATE OR REPLACE TRIGGER insert_auto_trigger BEFORE insert ON insert_auto FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select SEQ_INSERT_AUTO.nextval into :new.id from dual; END; ;


-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id INTEGER NOT NULL,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY (id)
);

