 -- Schema for testing the ADOdb session management feature
 
 DROP TABLE IF EXISTS session_test;

CREATE TABLE session_test (
  sesskey VARCHAR( 64 ) NOT NULL DEFAULT '',
  expiry DATETIME NOT NULL ,
  expireref VARCHAR( 250 ) DEFAULT '',
  created DATETIME NOT NULL ,
  modified DATETIME NOT NULL ,
  sessdata VARCHAR(MAX),
  PRIMARY KEY ( sesskey )
);

CREATE INDEX sess2_expiry ON session_test( expiry );
CREATE INDEX sess2_expireref ON session_test( expireref );


INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session001',
  DATEADD(s, -36000, GETDATE()),
  'PASTEXPIRY',
  DATEADD(s, -36000, GETDATE()),
  DATEADD(s, -36000, GETDATE()),
  NULL
  );

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session002',
   DATEADD(s, 36000, GETDATE()),
  'FUTUREEXPIRY',
   DATEADD(s, 36000, GETDATE()),
   DATEADD(s, 36000, GETDATE()),
   NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session003',
   DATEADD(s, -72000, GETDATE()),
  'PASTEXPIRY',
   DATEADD(s, -72000, GETDATE()),
   DATEADD(s, -72000, GETDATE()),
  NULL
  );

  INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session004',
   DATEADD(s, 72000, GETDATE()),
  'FUTUREEXPIRY',
   DATEADD(s, 72000, GETDATE()),
   DATEADD(s, 72000, GETDATE()),
  NULL
  );