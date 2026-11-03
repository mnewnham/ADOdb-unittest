 -- Firebird Schema for testing the ADOdb session management feature
 

RECREATE TABLE session_test (
  sesskey VARCHAR( 64 ) NOT NULL,
  expiry TIMESTAMP NOT NULL ,
  expireref VARCHAR( 250 ),
  created TIMESTAMP NOT NULL ,
  modified TIMESTAMP NOT NULL ,
  sessdata BLOB,
  PRIMARY KEY ( sesskey )

);
CREATE INDEX sess2_expiry ON session_test( expiry );
CREATE INDEX sess2_expireref ON session_test( expireref );

