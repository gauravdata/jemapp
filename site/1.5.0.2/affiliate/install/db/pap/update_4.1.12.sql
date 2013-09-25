ALTER TABLE qu_pap_users
 ADD notificationemail VARCHAR(80) AFTER originalparentuserid;

ALTER TABLE qu_pap_banners
 CHANGE dateinserted dateinserted DATE NOT NULL,
 ADD data5 TEXT ASCII AFTER data4,
 ADD data6 TEXT ASCII,
 ADD data7 TEXT ASCII;