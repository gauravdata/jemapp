UPDATE qu_pap_transactions SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_transactions CHANGE visitorid visitorid CHAR(32) NULL DEFAULT NULL;

UPDATE IGNORE qu_pap_visitors SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
DELETE FROM qu_pap_visitors WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_visitors CHANGE visitorid visitorid CHAR(32) NOT NULL;

UPDATE qu_pap_visitoraffiliates SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_visitoraffiliates CHANGE visitorid visitorid CHAR(32) NOT NULL;

UPDATE qu_pap_visits0 SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_visits0 CHANGE visitorid visitorid CHAR(32) NULL DEFAULT NULL;

UPDATE qu_pap_visits1 SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_visits1 CHANGE visitorid visitorid CHAR(32) NULL DEFAULT NULL;

UPDATE qu_pap_visits2 SET visitorid = SUBSTR(visitorid, 1, 32) WHERE LENGTH(visitorid) > 32;
ALTER TABLE qu_pap_visits2 CHANGE visitorid visitorid CHAR(32) NULL DEFAULT NULL;
