-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table;

CREATE TABLE dictionary_change_test_table (
	id INT IDENTITY(1,1) PRIMARY KEY,
	date_field DATE NOT NULL,
	integer_field INT DEFAULT 0,
	decimal_field_to_modify DECIMAL(8,4) NOT NULL DEFAULT 0,
	boolean_field_to_rename BIT DEFAULT 0 NOT NULL,
	boolean_field_to_change_default BIT NOT NULL,
	droppable_field decimal(10,6) NOT NULL,
	varchar_field VARCHAR(50),
	nvarchar_field NVARCHAR(50),
	smallint_to_expand SMALLINT,
	xl_field VARBINARY(MAX),
	
);

ALTER TABLE dictionary_change_test_table ADD CONSTRAINT df_date_field DEFAULT N'2030-01-01' FOR date_field;
ALTER TABLE dictionary_change_test_table ADD CONSTRAINT df_boolean_field_to_change_default DEFAULT 1 FOR boolean_field_to_change_default;
ALTER TABLE dictionary_change_test_table ADD CONSTRAINT df_droppable_field DEFAULT  80.111 FOR droppable_field;

CREATE UNIQUE INDEX index_to_drop ON dictionary_change_test_table (varchar_field);

