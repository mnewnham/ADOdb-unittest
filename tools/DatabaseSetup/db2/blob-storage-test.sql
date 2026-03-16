
-- blob_storage_table is used to test blob storage
DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field SMALLINT NOT NULL DEFAULT 0,
	blob_field BLOB(100M),
	PRIMARY KEY (id)
);

