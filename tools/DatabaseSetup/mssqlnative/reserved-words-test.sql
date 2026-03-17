
-- This table is used to test the quoting of table and field names
DROP TABLE IF EXISTS [table_name];
CREATE TABLE [table_name] (
	[id] INT IDENTITY(1,1) PRIMARY KEY,
	[column_name] VARCHAR(20)
);
