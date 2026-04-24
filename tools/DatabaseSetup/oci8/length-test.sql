-- length-test is loaded with 20 character defaults for testing the length functions

DROP TABLE IF EXISTS length_test;

CREATE TABLE length_test (
    id INTEGER NOT NULL,
    char_field CHAR(30) DEFAULT 'TEST567890TEST567890',
    varchar2_field VARCHAR2(30) DEFAULT 'TEST567890TEST567890',
    nchar_field NCHAR(30) DEFAULT 'TEST567890TEST567890',
    nvarchar2_field VARCHAR2(30) DEFAULT 'TEST567890TEST567890',
    clob_field CLOB DEFAULT 'TEST567890TEST567890',
    nclob_field NCLOB DEFAULT 'TEST567890TEST567890'
);


