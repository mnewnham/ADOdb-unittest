

-- This table is used to test the quoting of table and field names
DROP TABLE IF EXISTS 'table_name';
CREATE TABLE 'table_name' (
	'id' INTEGER PRIMARY KEY AUTOINCREMENT,
	'column_name' VARCHAR(20)
);