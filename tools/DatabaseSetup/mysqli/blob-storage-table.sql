-- blob_storage_table is built for blob testing

DROP TABLE IF EXISTS blob_storage_table;

CREATE TABLE blob_storage_table (
	id INT NOT NULL AUTO_INCREMENT,
	integer_field INT(2) DEFAULT 0,
	blob_field LONGBLOB,
	clob_field LONGTEXT,
	varchar_field VARCHAR(20),
	PRIMARY KEY(id)
);