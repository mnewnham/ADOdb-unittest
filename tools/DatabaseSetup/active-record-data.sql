-- Sets up the test data for active record tests

INSERT INTO persons(name_first,name_last,birth_date) VALUES('SARAH','CONNOR','1962-10-20');
INSERT INTO persons(name_first,name_last,birth_date) VALUES('EDITH','WATERBURY','1955-09-19');


-- Person_id depends on the auto increment field of persons which is weak
INSERT INTO children(person_id,name_first,name_last,birth_date) VALUES(1,'JOHN','CONNOR','1995-03-12');
INSERT INTO children(person_id,name_first,name_last,birth_date) VALUES(2,'BOBBIE','WATERBURY','1970-08-04');
INSERT INTO children(person_id,name_first,name_last,birth_date) VALUES(2,'PHYLISS','WATERBURY','1973-04-14');
INSERT INTO children(person_id,name_first,name_last,birth_date) VALUES(2,'PETER','WATERBURY','1976-09-02');