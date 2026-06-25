 -- Schema for testing the ADOdb session management feature
 
DROP TABLE IF EXISTS session_test;

CREATE TABLE session_test (
  sesskey VARCHAR( 64 ) DEFAULT '',
  expiry TIMESTAMP NOT NULL ,
  expireref VARCHAR( 250 ) DEFAULT '',
  created TIMESTAMP NOT NULL ,
  modified TIMESTAMP NOT NULL ,
  sessdata CLOB,
  PRIMARY KEY ( sesskey )
);

-- sessdata CLOB,
-- sessdata VARCHAR(4000)	DEFAULT '',

CREATE INDEX sess2_expiry ON session_test (expiry);
CREATE INDEX sess2_expireref ON session_test (expireref);

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session001',
  SYSDATE + INTERVAL '-36000' SECOND ,
  'PASTEXPIRY',
   SYSDATE + INTERVAL '-36000' SECOND ,
   SYSDATE + INTERVAL '-36000' SECOND ,
  NULL
);

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session002',
   SYSDATE + INTERVAL '36000' SECOND ,
  'FUTUREEXPIRY',
   SYSDATE + INTERVAL '36000' SECOND ,
   SYSDATE + INTERVAL '36000' SECOND ,
  NULL
);

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session003',
   SYSDATE + INTERVAL '-72000' SECOND ,
  'PASTEXPIRY',
   SYSDATE + INTERVAL '-72000' SECOND ,
   SYSDATE + INTERVAL '-72000' SECOND ,
  NULL
);

INSERT INTO session_test(sesskey, expiry, expireref, created, modified, sessdata) values (
  'session004',
   SYSDATE + INTERVAL '72000' SECOND ,
  'FUTUREEXPIRY',
   SYSDATE + INTERVAL '72000' SECOND ,
   SYSDATE + INTERVAL '72000' SECOND ,
  NULL
  );

