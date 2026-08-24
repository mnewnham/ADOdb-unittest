
-- This table is used to test the quoting of table and field names
-- It uses a reserved word as the table name and column names
DROP TABLE IF EXISTS "SELECT";
CREATE TABLE "SELECT" (
	"ID" INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
	"COLUMN_NAME" VARCHAR(20)
);