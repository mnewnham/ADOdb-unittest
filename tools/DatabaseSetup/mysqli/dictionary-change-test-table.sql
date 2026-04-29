-- This table is used to test changes to columns via dictionary
-- functions. not used by createTableSql
-- Reprocessed for each test to provide a standard baseline

DROP TABLE IF EXISTS dictionary_change_test_table;
DROP TABLE IF EXISTS dt_foreign_key_target_1;

-- Creates a first foreign reference for foreign_key_source
CREATE TABLE dt_foreign_key_target_1 (
	id_1 INT NOT NULL AUTO_INCREMENT,
    integer_field_1 INT,
	PRIMARY KEY(id_1)
);

-- Must provide a qualifying index to match the fk definition
CREATE UNIQUE INDEX dt_fk1_proxy ON dt_foreign_key_target_1 (integer_field_1);

CREATE TABLE dictionary_change_test_table (
	id INT NOT NULL AUTO_INCREMENT,
	date_field DATE NOT NULL DEFAULT '2030-01-01',
	integer_field INT DEFAULT 0,
	decimal_field_to_modify DECIMAL(8.4) NOT NULL DEFAULT 0,
	boolean_field_to_rename BOOLEAN DEFAULT 0,
	boolean_field_to_change_default BOOLEAN DEFAULT 1,
	droppable_field decimal(10.6) NOT NULL DEFAULT 80.111,
	droppable_integer_field INT DEFAULT 0,
	enum_field_to_keep ENUM('duplo','lego','meccano'),
	varchar_field VARCHAR(50),
	nvarchar_field NVARCHAR(50),
	smallint_to_expand SMALLINT,
	xl_field LONGTEXT,
	PRIMARY KEY(id),
	UNIQUE INDEX index_to_drop (varchar_field),
	FOREIGN KEY (droppable_integer_field) REFERENCES dt_foreign_key_target_1(integer_field_1)
);
