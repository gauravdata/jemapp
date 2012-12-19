ALTER TABLE qu_pap_clicks ADD accountid CHAR(8) ASCII NOT NULL AFTER clickid;

ALTER TABLE qu_pap_impressions ADD accountid CHAR(8) ASCII NOT NULL AFTER impressionid;

ALTER TABLE qu_pap_transactions ADD accountid CHAR(8) ASCII NOT NULL AFTER transid;

UPDATE qu_pap_clicks SET accountid = 'default1';

UPDATE qu_pap_impressions SET accountid = 'default1';

UPDATE qu_pap_transactions SET accountid = 'default1';