-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline
-- enum_field_to_keep ENUM('duplo','lego','meccano'), //NOT AVAILABLE IN ORACLE
DROP TABLE IF EXISTS dictionary_change_test_table;
DROP SEQUENCE IF EXISTS dictionary_change_test_table_seq;
DROP TRIGGER IF EXISTS dictionary_change_test_table_t;
--DROP CONSTRAINT fks_fk_1;

DROP TABLE IF EXISTS dt_foreign_key_target_1;

-- Creates a first foreign reference for foreign_key_source
CREATE TABLE dt_foreign_key_target_1 (
	id_1 INTEGER NOT NULL,
    integer_field_1 INTEGER,
	PRIMARY KEY(integer_field_1)
);



CREATE TABLE dictionary_change_test_table (
	id INTEGER NOT NULL ,
	date_field DATE NOT NULL,
	integer_field SMALLINT DEFAULT 0,
	droppable_integer_field SMALLINT DEFAULT 0,
	decimal_field_to_modify NUMBER(8,4) DEFAULT 0,
	boolean_field_to_rename NUMBER(1,0),
	boolean_field_to_change_default NUMBER(1,1),
	droppable_field NUMBER(10,6) NOT NULL,
	
	varchar_field VARCHAR(50),
	nvarchar_field NVARCHAR2(50),
	smallint_to_expand SMALLINT,
	xl_field BLOB,
	PRIMARY KEY(id)
);

CREATE UNIQUE INDEX index_to_drop ON dictionary_change_test_table(varchar_field);
CREATE INDEX droppable_field_index ON dictionary_change_test_table (droppable_field);
CREATE UNIQUE INDEX droppable_integer_field_index ON dictionary_change_test_table (droppable_integer_field);


CREATE SEQUENCE dictionary_change_test_table_seq
    INCREMENT BY 1
    START WITH 1;

-- This statement has an extraneous ; at end to force 
-- the procedure to be created in Oracle. It will be stripped
-- by the schema loader
-- DO NOT REMOVE

CREATE OR REPLACE TRIGGER dictionary_change_test_table_t BEFORE insert ON dictionary_change_test_table FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select dictionary_change_test_table_seq.nextval into :new.id from dual; END; ;

-- FOREIGN KEY (droppable_integer_field) REFERENCES dt_foreign_key_target_1(integer_field_1),

--ALTER TABLE dictionary_change_test_table ADD CONSTRAINT fks_fk_1
--FOREIGN KEY (droppable_integer_field)
--	REFERENCES dt_foreign_key_target_1(integer_field_1);
