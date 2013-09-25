ALTER TABLE qu_pap_payouthistory ADD accountid CHAR(8) NOT NULL;
UPDATE qu_pap_payouthistory SET accountid = 'default1';