-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table;

-- Missing ENUM support until added to ADOdb
-- enum_field_to_keep ENUM('duplo','lego','meccano'),
-- No direct NVARCHAR type

CREATE TABLE dictionary_change_test_table (
	id SERIAL PRIMARY KEY,
	date_field DATE NOT NULL DEFAULT '2030-01-01',
	integer_field SMALLINT DEFAULT 0,
	decimal_field_to_modify DECIMAL(8,4) DEFAULT 0.0,
	boolean_field_to_rename BOOLEAN DEFAULT FALSE,
	boolean_field_to_change_default BOOLEAN DEFAULT TRUE,
    droppable_field decimal(10,6) NOT NULL DEFAULT 80.111,
	varchar_field VARCHAR(50) DEFAULT '',
	nvarchar_field VARCHAR(50) DEFAULT ''
);
CREATE UNIQUE INDEX index_to_drop ON dictionary_change_test_table (varchar_field);

