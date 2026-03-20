
-- blob_storage_table is built for blob testing
DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	integer_field INT(2) DEFAULT 0,
	blob_field BLOB,
	clob_field TEXT,
	varchar_field VARCHAR(20)
);