-- Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;

CREATE TABLE persons (
    id INT NOT NULL AUTO_INCREMENT,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY  (id)
) ENGINE=INNODB;

CREATE TABLE children (
    id INT NOT NULL AUTO_INCREMENT,
    person_id INT NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY (id)
) ENGINE=INNODB;