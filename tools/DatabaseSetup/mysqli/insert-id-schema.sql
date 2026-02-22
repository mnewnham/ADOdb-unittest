-- Insert Id test suite for MySQLi driver

-- 
DROP TABLE IF EXISTS insert_auto;
DROP TABLE IF EXISTS insert_manual;

-- Creates a simple table that has an auto-increment key
CREATE TABLE insert_auto (
	id INT NOT NULL AUTO_INCREMENT,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY (id)
) ENGINE=INNODB;

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE insert_manual (
	id INT NOT NULL,
	integer_field INTEGER NOT NULL,
	PRIMARY KEY (id)
) ENGINE=INNODB;
