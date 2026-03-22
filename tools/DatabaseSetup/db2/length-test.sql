-- length-test is loaded with 20 character defaults for testing the length functions

DROP TABLE IF EXISTS length_test;

CREATE TABLE length_test (
id INTEGER NOT NULL,
    char_field CHAR(30) DEFAULT 'TEST567890TEST567890',
    varchar_field VARCHAR(30) DEFAULT 'TEST567890TEST567890'
);

INSERT INTO length_test (id) VALUES (1);
