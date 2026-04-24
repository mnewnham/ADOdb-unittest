-- length-test is loaded with 20 character defaults for testing the length functions

DROP TABLE IF EXISTS length_test;

CREATE TABLE length_test (
id INTEGER NOT NULL,
    char_field CHAR(30) DEFAULT 'TEST567890TEST567890',
    varchar_field VARCHAR(30) DEFAULT 'TEST567890TEST567890',
    text_field TEXT  
);

INSERT INTO length_test (id, text_field, char_field, varchar_field) VALUES (1, 'TEST567890TEST567890', 'TEST567890TEST567890','TEST567890TEST567890');
