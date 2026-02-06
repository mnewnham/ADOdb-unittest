-- metatype_test to test all of the various meta and actual type
-- Contains no data
-- Compatiple with MySQL 8.0 
-- ACTUALTYPE_METATYPE_MYSQLTYPE_FIELD


DROP TABLE IF EXISTS metatype_test;

CREATE TABLE metatype_test (
	id INT NOT NULL AUTO_INCREMENT,
	varchar_c_char_field CHAR(10),
	varchar_c_varchar_field VARCHAR(10),
	nvarchar_c2_binary_field BINARY(10),
	nvarchar_c2_varbinary_field VARBINARY(10),
	longblob_b_tinyblob_field TINYBLOB,
	text_x_tinytext_field TINYTEXT,
	text_x_text_field TEXT(1000),
	longblob_b_blob_field BLOB(1000),
	text_x_mediumtext_field MEDIUMTEXT,
	longblob_b_mediumblob_field MEDIUMBLOB,
    text_x_longtext_field LONGTEXT,
	longblob_b_longblob_field LONGBLOB,
    enum_e_enum_field ENUM('cheddar','shropshire','wenslydale'),
    enum_e_set_field SET('plaice','haddock','cod'),
    boolean_l_bit_field BIT(1),
    
    boolean_l_boolean_bool BOOLEAN,

    boolean_l_tinyint_field TINYINT(1),
    smallint_i2_smallint_field SMALLINT(10),
    integer_i4_mediumint_field MEDIUMINT(10),
    integer_i_integer_field INTEGER(10),
    bigint_i8_bigint_field BIGINT(10),

    double_f_smallfloat_field FLOAT(10),
    double_f_bigfloat_field FLOAT(26),
  
    double_f_double_field DOUBLE(14, 12),
    double_f_doubleprecision_field DOUBLE PRECISION(16, 12),

    numeric_n_decimal_field DECIMAL(16, 2),    

    datetime_t_datetime_field DATETIME,
    datetime_t_timestamp_field TIMESTAMP,
	date_d_date_field DATE,
	datetime_t_time_field TIME,
	date_d_year_field YEAR,

    PRIMARY KEY(id)

) ENGINE=INNODB;

--   float_f_double_field DOUBLE(12),
--   real_r_double_precision_field DOUBLE PRECISION(12),