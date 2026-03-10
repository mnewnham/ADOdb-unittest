-- Insert Id test suite for SQL Server driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id BIGINT IDENTITY(1,1),
	integer_field INTEGER NOT NULL,
	PRIMARY KEY(id)
);

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id BIGINT,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY(id)
);
