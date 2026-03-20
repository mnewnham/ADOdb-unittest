
-- This table is used to test the quoting of table and field names
-- It uses a reserved word as the table name and column names
DROP TABLE IF EXISTS "table_name";
CREATE TABLE "table_name" (
	"id" INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	"column_name" VARCHAR(20)
);