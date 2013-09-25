ALTER TABLE `qu_pap_users` ADD `originalparentuserid` CHAR( 8 ) NULL ;
UPDATE `qu_pap_users` SET originalparentuserid = parentuserid;