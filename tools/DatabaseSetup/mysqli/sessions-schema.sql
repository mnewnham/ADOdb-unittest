 -- Schema for testing the ADOdb session management feature
 

CREATE TABLE session_test (
  sesskey VARCHAR( 64 ) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  expiry DATETIME NOT NULL ,
  expireref VARCHAR( 250 ) DEFAULT '',
  created DATETIME NOT NULL ,
  modified DATETIME NOT NULL ,
  sessdata LONGTEXT,
  PRIMARY KEY ( sesskey ) ,
  INDEX sess2_expiry( expiry ),
  INDEX sess2_expireref( expireref )
);
