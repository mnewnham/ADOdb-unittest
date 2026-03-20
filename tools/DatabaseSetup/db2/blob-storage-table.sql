
-- blob_storage_table is used to test blob storage
DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field INTEGER,
	blob_field BLOB(100M),
	clob_field CLOB,
	varchar_field VARCHAR(20)
);

