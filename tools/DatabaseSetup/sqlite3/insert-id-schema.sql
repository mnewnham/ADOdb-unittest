-- Insert Id test suite for SQLite3 driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	integer_field INTEGER NOT NULL
);

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id INTEGER PRIMARY KEY,
	integer_field INTEGER NOT NULL
);
