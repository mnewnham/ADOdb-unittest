-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table_renamed;
DROP TABLE IF EXISTS dictionary_change_test_table;
DROP TABLE IF EXISTS dt_foreign_key_target_1;

-- Creates a first foreign reference for foreign_key_source
CREATE TABLE dt_foreign_key_target_1 (
	id_1 INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
    integer_field_1 INTEGER NOT NULL,
	PRIMARY KEY(id_1, integer_field_1)
);
-- Missing ENUM support until added to ADOdb
-- enum_field_to_keep ENUM('duplo','lego','meccano'),
-- No direct NVARCHAR type

CREATE TABLE dictionary_change_test_table (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY,
	date_field DATE NOT NULL DEFAULT '2030-01-01',
	integer_field SMALLINT DEFAULT 0,
	decimal_field_to_modify DECIMAL(8,4) DEFAULT 0.0,
	boolean_field_to_rename BOOLEAN DEFAULT FALSE,
	boolean_field_to_change_default BOOLEAN DEFAULT TRUE,
    droppable_field decimal(10,6) NOT NULL DEFAULT 80.111,
	droppable_integer_field INT DEFAULT 0,
	varchar_field CHAR(50) DEFAULT '',
	nvarchar_field VARCHAR(50) DEFAULT '',
	smallint_to_expand SMALLINT,
	xl_field BYTEA,
	FOREIGN KEY (id,droppable_integer_field) REFERENCES dt_foreign_key_target_1(id_1, integer_field_1)
);
CREATE UNIQUE INDEX index_to_drop ON dictionary_change_test_table (varchar_field);
