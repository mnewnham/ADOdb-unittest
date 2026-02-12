-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table;

CREATE TABLE dictionary_change_test_table (
	id INTEGER NOT NULL ,
	date_field DATE NOT NULL DEFAULT '2030-01-01',
	integer_field SMALLINT DEFAULT 0,
	decimal_field_to_modify NUMBER(8,4) NOT NULL DEFAULT 0,
	boolean_field_to_rename NUMBER(1,0),
	boolean_field_to_change_default NUMBER(1,1),
	droppable_field NUMBER(10,6) NOT NULL DEFAULT 80.111,
	enum_field_to_keep ENUM('duplo','lego','meccano'),
	varchar_field VARCHAR(50),
	nvarchar_field NVARCHAR(50),
	smallint_to_expand SMALLINT,
	xl_field BLOB,
	PRIMARY KEY(id),
	UNIQUE INDEX index_to_drop (varchar_field)
);

CREATE SEQUENCE dictionary_change_test_table_seq
    INCREMENT BY 1
    START WITH 1;

-- This statement has an extraneous ; at end to force 
-- the procedure to be created in Oracle. It will be stripped
-- by the schema loader
CREATE OR REPLACE TRIGGER testable_1_t BEFORE insert ON dictionary_change_test_table FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select dictionary_change_test_table_seq.nextval into :new.id from dual; END; ;