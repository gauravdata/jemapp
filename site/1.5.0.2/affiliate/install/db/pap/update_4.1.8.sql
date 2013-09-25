ALTER TABLE qu_g_settings ADD INDEX ( name );
ALTER TABLE qu_pap_monthlyimpressions ADD INDEX ( userid , campaignid , bannerid , month );
ALTER TABLE qu_pap_dailyimpressions ADD INDEX ( userid , campaignid , bannerid , day );
ALTER TABLE qu_pap_dailyclicks ADD INDEX ( userid , campaignid , bannerid , day );
ALTER TABLE qu_pap_monthlyclicks ADD INDEX ( userid , campaignid , bannerid , month );