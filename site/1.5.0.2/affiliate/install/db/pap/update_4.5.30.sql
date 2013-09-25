ALTER TABLE qu_pap_bannersinrotators 
    ADD `valid_from` DATETIME NULL , 
    ADD `valid_until` DATETIME NULL ,
    ADD `archive` CHAR(1) NULL ;
    
ALTER TABLE qu_pap_cachedbanners 
    ADD `valid_from` DATETIME NULL , 
    ADD `valid_until` DATETIME NULL ;