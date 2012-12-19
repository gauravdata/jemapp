ALTER TABLE qu_pap_impressions0
 ADD parentbannerid CHAR(8) ASCII DEFAULT 'NULL' AFTER bannerid;
ALTER TABLE qu_pap_impressions1
 ADD parentbannerid CHAR(8) ASCII DEFAULT 'NULL' AFTER bannerid;
ALTER TABLE qu_pap_impressions2
 ADD parentbannerid CHAR(8) ASCII DEFAULT 'NULL' AFTER bannerid;
