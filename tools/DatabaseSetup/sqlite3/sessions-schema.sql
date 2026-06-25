 -- Schema for testing the ADOdb session management feature
 
 DROP TABLE IF EXISTS session_test;

CREATE TABLE session_test (
  sesskey VARCHAR( 64 ) NOT NULL DEFAULT '',
  expiry DATETIME NOT NULL ,
  expireref VARCHAR( 250 ) DEFAULT '',
  created DATETIME NOT NULL ,
  modified DATETIME NOT NULL ,
  sessdata LONGBLOB,
  PRIMARY KEY ( sesskey )

);
CREATE INDEX sess2_expiry ON session_test( expiry );
CREATE INDEX sess2_expireref ON session_test( expireref );


INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session001',
  DATETIME('now', '-36000 SECONDS') ,
  'PASTEXPIRY',
  DATETIME('now', '-36000 SECONDS') ,
  DATETIME('now', '-36000 SECONDS') ,
  NULL
  );

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session002',
  DATETIME('now', '+36000 SECONDS') ,
  'FUTUREEXPIRY',
  DATETIME('now', '+36000 SECONDS') ,
  DATETIME('now', '+36000 SECONDS') ,
  NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session003',
  DATETIME('now', '-72000 SECONDS') ,
  'PASTEXPIRY',
  DATETIME('now', '-72000 SECONDS') ,
  DATETIME('now', '-72000 SECONDS') ,
  NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session004',
  DATETIME('now', '+72000 SECONDS') ,
  'FUTUREEXPIRY',
  DATETIME('now', '+72000 SECONDS') ,
  DATETIME('now', '+72000 SECONDS') ,
  NULL
  );