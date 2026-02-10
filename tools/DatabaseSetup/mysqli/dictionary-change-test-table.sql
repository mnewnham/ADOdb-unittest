-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table;

CREATE TABLE dictionary_change_test_table (
	id INT NOT NULL AUTO_INCREMENT,
	date_field DATE NOT NULL DEFAULT '2030-01-01',
	integer_field INT DEFAULT 0,
	decimal_field_to_modify DECIMAL(8.4) NOT NULL DEFAULT 0,
	boolean_field_to_rename BOOLEAN DEFAULT 0,
	boolean_field_to_change_default BOOLEAN DEFAULT 1,
	droppable_field decimal(10.6) NOT NULL DEFAULT 80.111,
	enum_field_to_keep ENUM('duplo','lego','meccano'),
	varchar_field VARCHAR(50),
	nvarchar_field NVARCHAR(50),
	PRIMARY KEY(id),
	UNIQUE INDEX index_to_drop (varchar_field)
);