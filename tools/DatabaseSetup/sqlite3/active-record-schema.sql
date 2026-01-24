-- Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;

CREATE TABLE persons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);

CREATE TABLE children (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    person_id INTEGER NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);