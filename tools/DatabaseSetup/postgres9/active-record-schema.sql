-- Postgres Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;

CREATE TABLE persons (
    id SERIAL PRIMARY KEY,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    drivers_license_no VARCHAR(20) DEFAULT 'ABC 123'
);

CREATE TABLE children (
    id SERIAL PRIMARY KEY,
    person_id BIGINT NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);