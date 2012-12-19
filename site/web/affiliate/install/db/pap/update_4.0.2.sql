ALTER TABLE `qu_pap_commissiongroups` CHANGE `isdefault` `isdefault` CHAR( 1 ) NOT NULL DEFAULT 'N' COMMENT 'Y - Yes N - No';
UPDATE qu_pap_commissiongroups SET isdefault = 'Y' WHERE isdefault = '1';
UPDATE qu_pap_commissiongroups SET isdefault = 'N' WHERE isdefault = '0';