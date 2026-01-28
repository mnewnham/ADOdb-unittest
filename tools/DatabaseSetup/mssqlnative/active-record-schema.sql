-- Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;

CREATE TABLE persons (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);

CREATE TABLE children (
    id INT IDENTITY(1,1) PRIMARY KEY,
    person_id INT NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);