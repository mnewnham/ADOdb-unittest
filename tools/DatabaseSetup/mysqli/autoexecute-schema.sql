DROP TABLE IF EXISTS autoexecute;

CREATE TABLE autoexecute (
	id INT NOT NULL AUTO_INCREMENT,
	varchar_field VARCHAR(20),
	date_field DATE,
	integer_field INT(2) DEFAULT 0,
	decimal_field decimal(12.2) DEFAULT 0,
	empty_field VARCHAR(240) DEFAULT '',
	number_run_field INT(4) DEFAULT 0,
	PRIMARY KEY(id),
) ENGINE=INNODB;

