ALTER TABLE qu_pap_banners
 ADD data8 TEXT ASCII AFTER data7,
 ADD data9 TEXT ASCII;
 
ALTER TABLE qu_pap_transactions ADD fixedcost FLOAT NOT NULL DEFAULT '0'; 