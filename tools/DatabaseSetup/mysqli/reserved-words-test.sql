

-- This table is used to test the quoting of table and field names
-- It uses a reserved word as the table name and column names
DROP TABLE IF EXISTS `select`;
CREATE TABLE `select` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`column_name` VARCHAR(20),
	PRIMARY KEY(`id`)
) ENGINE=INNODB;


