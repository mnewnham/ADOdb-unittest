-- Insert Id test suite for Postgres driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id SERIAL PRIMARY KEY,
	integer_field SMALLINT NOT NULL
);

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id SMALLINT PRIMARY KEY NOT NULL,
	integer_field INTEGER NOT NULL
);
