-- Test tables for the active record module

DROP TABLE IF EXISTS persons;
DROP TABLE IF EXISTS children;
DROP SEQUENCE IF EXISTS persons_seq;
DROP SEQUENCE IF EXISTS children_seq;
DROP TRIGGER IF EXISTS persons_t;
DROP TRIGGER IF EXISTS children_t;



CREATE TABLE persons (
    id INTEGER NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL
);

-- Creates an auto-increment column
CREATE SEQUENCE persons_seq
    INCREMENT BY 1
    START WITH 1;

CREATE OR REPLACE TRIGGER persons_t BEFORE insert ON persons FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select persons_seq.nextval into :new.id from dual; END; ;

CREATE TABLE children (
    id INTEGER NOT NULL,
    person_id INTEGER NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY (id)
);

CREATE OR REPLACE TRIGGER children_t BEFORE insert ON children FOR EACH ROW WHEN (NEW.id IS NULL OR NEW.id=0) BEGIN select children_seq.nextval into :new.id from dual; END; ;