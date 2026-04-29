
-- This table is used to test the quoting of table and field names
DROP TABLE IF EXISTS [select];
CREATE TABLE [select] (
	[id] INT IDENTITY(1,1) PRIMARY KEY,
	[column_name] VARCHAR(20)
);
