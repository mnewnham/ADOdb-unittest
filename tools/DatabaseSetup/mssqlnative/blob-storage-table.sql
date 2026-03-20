-- blob_storage_table is used for blob handling
DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
    id INT IDENTITY(1,1) PRIMARY KEY,
    integer_field INT DEFAULT 0,
	blob_field VARBINARY(MAX),
    clob_field TEXT,
    varchar_field VARCHAR(20)
);
