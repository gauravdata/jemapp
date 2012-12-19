ALTER TABLE qu_pap_transactions ADD saleid CHAR(8) NULL;
UPDATE qu_pap_transactions SET saleid = transid WHERE saleid IS null;