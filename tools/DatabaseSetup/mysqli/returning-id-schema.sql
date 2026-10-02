-- Returning Id test for MySQLi driver connecting to MariaDB

-- 

-- Creates a simple table where the key field must be incremented manually
CREATE TABLE returning_id_test (
	id INT NOT NULL,
	name VARCHAR(50),
	PRIMARY KEY (id)
) ENGINE=INNODB;
