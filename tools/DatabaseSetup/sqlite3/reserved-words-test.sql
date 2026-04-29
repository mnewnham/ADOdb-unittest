

-- This table is used to test the quoting of table and field names
DROP TABLE IF EXISTS 'select';
CREATE TABLE 'select' (
	'id' INTEGER PRIMARY KEY AUTOINCREMENT,
	'column_name' VARCHAR(20)
);