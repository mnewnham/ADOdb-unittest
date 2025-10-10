-- IBM DB2 Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;

CREATE TABLE persons (
    id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY  (id)
);

CREATE TABLE children (
   id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
    person_id INT NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY (id)
);