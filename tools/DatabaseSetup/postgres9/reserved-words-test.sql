-- This table is used to test the quoting of table and field names
-- It uses a reserved word as the table name and column names
DROP TABLE IF EXISTS "select";
CREATE TABLE "select" (
	"id" SERIAL PRIMARY KEY,
	"column_name" VARCHAR(20)
	);
