ALTER TABLE qu_pap_affiliatetrackingcodes ADD rtype CHAR(1) NULL DEFAULT NULL;
UPDATE qu_pap_affiliatetrackingcodes SET rtype = 'S';