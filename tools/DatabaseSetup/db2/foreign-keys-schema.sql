-- Foreign keys test suite for IBM DB2 driver
-- No data is loaded into these tables

-- Must drop foreign_key_target_1 and 2 before foreign_key_source because of foreign key constraints
DROP TABLE IF EXISTS foreign_key_target_1;
DROP TABLE IF EXISTS foreign_key_target_2;
DROP TABLE IF EXISTS foreign_key_source;

-- Creates a first foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_1 (
	id_1 INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field_1 SMALLINT NOT NULL DEFAULT 0,
	PRIMARY KEY (id_1,integer_field_1)
);

-- Creates a second foreign reference for foreign_key_source
CREATE TABLE foreign_key_target_2 (
	id_2 INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field_2 SMALLINT NOT NULL DEFAULT 0,
	PRIMARY KEY (id_2,integer_field_2)
);

-- foreign_key_source links to constraints above

CREATE TABLE foreign_key_source (
	id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	integer_field SMALLINT NOT NULL DEFAULT 0,
	tt_id_1 INTEGER NOT NULL,
	tt_id_2 INTEGER NOT NULL,
	PRIMARY KEY (id)
);

CREATE UNIQUE INDEX fks ON foreign_key_source (integer_field);

ALTER TABLE foreign_key_source
FOREIGN KEY (tt_id_1,integer_field)
	REFERENCES foreign_key_target_1(id_1,integer_field_1)
		ON UPDATE NO ACTION
		ON DELETE CASCADE;

ALTER TABLE foreign_key_source
FOREIGN KEY (tt_id_2,integer_field)
	REFERENCES foreign_key_target_2(id_2,integer_field_2)
		ON UPDATE NO ACTION
		ON DELETE CASCADE;