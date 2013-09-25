ALTER TABLE qu_pap_campaigns ADD `isdefault` CHAR(1) NOT NULL DEFAULT 'N' AFTER networkstatus;

UPDATE qu_pap_campaigns c
LEFT JOIN qu_pap_campaigns tmpc ON c.accountid=tmpc.accountid AND c.dateinserted > tmpc.dateinserted
SET c.isdefault = 'Y'
WHERE tmpc.dateinserted IS NULL;