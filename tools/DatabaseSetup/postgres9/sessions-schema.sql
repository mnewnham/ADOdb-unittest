 -- Schema for testing the ADOdb session management feature
 
 DROP TABLE IF EXISTS session_test;

CREATE TABLE session_test (
  sesskey VARCHAR( 64 ) NOT NULL DEFAULT '',
  expiry TIMESTAMP NOT NULL ,
  expireref VARCHAR( 250 ) DEFAULT '',
  created TIMESTAMP NOT NULL ,
  modified TIMESTAMP NOT NULL ,
  sessdata BYTEA,
  PRIMARY KEY ( sesskey )

);
CREATE INDEX sess2_expiry ON session_test( expiry );
CREATE INDEX sess2_expireref ON session_test( expireref );
-- sessdata BYTEA,

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session001',
  CURRENT_TIMESTAMP-INTERVAL'36000 SECONDS' ,
  'PASTEXPIRY',
  CURRENT_TIMESTAMP-INTERVAL'36000 SECONDS' ,
  CURRENT_TIMESTAMP-INTERVAL'36000 SECONDS' ,
  NULL
  );

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session002',
  CURRENT_TIMESTAMP+INTERVAL'36000 SECONDS' ,
  'FUTUREEXPIRY',
  CURRENT_TIMESTAMP+INTERVAL'36000 SECONDS' ,
  CURRENT_TIMESTAMP+INTERVAL'36000 SECONDS' ,
  NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session003',
  CURRENT_TIMESTAMP-INTERVAL'72000 SECONDS' ,
  'PASTEXPIRY',
  CURRENT_TIMESTAMP-INTERVAL'72000 SECONDS' ,
  CURRENT_TIMESTAMP-INTERVAL'72000 SECONDS' ,
  NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session004',
  CURRENT_TIMESTAMP+INTERVAL'72000 SECONDS' ,
  'FUTUREEXPIRY',
  CURRENT_TIMESTAMP+INTERVAL'72000 SECONDS' ,
  CURRENT_TIMESTAMP+INTERVAL'72000 SECONDS' ,
  NULL
  );