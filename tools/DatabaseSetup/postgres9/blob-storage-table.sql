-- blob_storage_table is used to blob handling
-- There is no data in this table
DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
    id SERIAL PRIMARY KEY,
    integer_field SMALLINT DEFAULT 0,
	blob_field BYTEA
);