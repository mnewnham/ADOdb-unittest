
DROP TABLE IF EXISTS blob_storage_table;
DROP SEQUENCE IF EXISTS blob_storage_table_seq;
DROP TRIGGER IF EXISTS blob_storage_table_t;

-- blob_storage_table is used to test blob data
-- There is no data in this table
CREATE TABLE blob_storage_table (
	id INTEGER NOT NULL,
	integer_field SMALLINT NOT NULL,
	blob_field BLOB,
    clob_field CLOB,
    varchar_field VARCHAR(20)
);

-- Creates an auto-increment column
CREATE SEQUENCE blob_storage_table_seq
    INCREMENT BY 1
    START WITH 1;

CREATE OR REPLACE TRIGGER blob_storage_table_t BEFORE insert ON blob_storage_table FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select blob_storage_table_seq.nextval into :new.id from dual; END; ;
