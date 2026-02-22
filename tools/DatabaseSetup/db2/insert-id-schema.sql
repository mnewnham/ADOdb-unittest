-- Insert Id test suite for IBM DB2 driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field INTEGER NOT NULL,
	PRIMARY KEY(id)
);

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id INTEGER NOT NULL,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY(id)
);
