-- Foreign keys test suite for MySQLi driver
-- No data is loaded into these tables

-- Must drop foreign_key_target_1 and 2 before foreign_key_source because of foreign key constraints
DROP TABLE IF EXISTS foreign_key_source;
DROP TABLE IF EXISTS foreign_key_target_1;
DROP TABLE IF EXISTS foreign_key_target_2;


-- Creates a first foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_1 (
	id_1 INT NOT NULL AUTO_INCREMENT,
    integer_field_1 INT(2),
	PRIMARY KEY (id_1,integer_field_1)
) ENGINE=INNODB;

-- Creates a second foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_2 (
	id_2 INT NOT NULL AUTO_INCREMENT,
    integer_field_2 INT(2),
	PRIMARY KEY (id_2,integer_field_2)
) ENGINE=INNODB;

-- foreign_key_source links to constraints above

CREATE TABLE foreign_key_source (
	id INT NOT NULL AUTO_INCREMENT,
    integer_field INT(2),
	tt_id_1 INT NOT NULL,
	tt_id_2 INT NOT NULL,
	PRIMARY KEY(id),
    FOREIGN KEY (tt_id_1,integer_field) REFERENCES foreign_key_target_1(id_1, integer_field_1),
    FOREIGN KEY (tt_id_2,integer_field) REFERENCES foreign_key_target_2(id_2, integer_field_2)
) ENGINE=INNODB;

CREATE UNIQUE INDEX fks ON foreign_key_source (integer_field);
