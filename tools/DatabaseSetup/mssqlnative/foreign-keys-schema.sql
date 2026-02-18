-- Foreign keys test suite for SQL Server driver
-- No data is loaded into these tables

-- Must drop foreign_key_target_1 and 2 before foreign_key_source because of foreign key constraints
DROP TABLE IF EXISTS foreign_key_source;
DROP TABLE IF EXISTS foreign_key_target_1;
DROP TABLE IF EXISTS foreign_key_target_2;


-- Creates a first foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_1 (
	id_1 INTEGER IDENTITY(1,1) PRIMARY KEY,
    integer_field_1 INTEGER
);

-- Must provide a qualifying index to match the fk definition
CREATE UNIQUE INDEX fk1_proxy ON foreign_key_target_1 (id_1, integer_field_1);

-- Creates a second foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_2 (
	id_2 INTEGER IDENTITY(1,1) PRIMARY KEY,
    integer_field_2 INTEGER
);

-- Must provide a qualifying index to match the fk definition
CREATE UNIQUE INDEX fk2_proxy ON foreign_key_target_2 (id_2, integer_field_2);


-- foreign_key_source links to constraints above

CREATE TABLE foreign_key_source (
	id INTEGER IDENTITY(1,1) PRIMARY KEY,
    integer_field INTEGER,
	tt_id_1 INTEGER NOT NULL,
	tt_id_2 INTEGER NOT NULL,
    FOREIGN KEY (tt_id_1,integer_field) REFERENCES foreign_key_target_1(id_1, integer_field_1),
    FOREIGN KEY (tt_id_2,integer_field) REFERENCES foreign_key_target_2(id_2, integer_field_2)
);

CREATE UNIQUE INDEX fks ON foreign_key_source (integer_field);
