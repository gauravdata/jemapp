UPDATE qu_pap_monthlyclicks
SET bannerid=IFNULL(bannerid,''),
campaignid=IFNULL(campaignid,''),
parentbannerid=IFNULL(parentbannerid,''),
countrycode=IFNULL(countrycode,''),
cdata1=IFNULL(cdata1,''),
cdata2=IFNULL(cdata2,''),
channel=IFNULL(channel,'');


CREATE  TABLE `qu_pap_monthlyclicks_temp` (
  `monthlyclickid` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userid` CHAR(8) NULL DEFAULT NULL ,
  `campaignid` CHAR(8) NULL DEFAULT NULL ,
  `bannerid` CHAR(8) NULL DEFAULT NULL ,
  `parentbannerid` VARCHAR(8) NULL DEFAULT NULL ,
  `countrycode` VARCHAR(2) NULL DEFAULT NULL ,
  `cdata1` VARCHAR(20) NULL DEFAULT NULL ,
  `cdata2` VARCHAR(20) NULL DEFAULT NULL ,
  `channel` VARCHAR(10) NULL DEFAULT NULL ,
  `month` DATE NULL DEFAULT NULL COMMENT 'first day of month' ,
  `raw_1` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_2` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_3` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_4` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_5` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_6` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_7` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_8` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_9` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_10` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_11` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_12` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_13` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_14` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_15` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_16` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_17` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_18` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_19` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_20` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_21` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_22` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_23` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_24` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_25` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_26` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_27` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_28` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_29` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_30` INT UNSIGNED NULL DEFAULT 0 ,
  `raw_31` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_1` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_2` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_3` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_4` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_5` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_6` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_7` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_8` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_9` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_10` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_11` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_12` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_13` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_14` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_15` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_16` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_17` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_18` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_19` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_20` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_21` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_22` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_23` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_24` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_25` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_26` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_27` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_28` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_29` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_30` INT UNSIGNED NULL DEFAULT 0 ,
  `unique_31` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_1` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_2` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_3` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_4` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_5` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_6` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_7` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_8` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_9` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_10` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_11` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_12` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_13` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_14` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_15` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_16` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_17` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_18` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_19` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_20` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_21` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_22` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_23` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_24` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_25` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_26` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_27` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_28` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_29` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_30` INT UNSIGNED NULL DEFAULT 0 ,
  `declined_31` INT UNSIGNED NULL DEFAULT 0 ,
  PRIMARY KEY (`monthlyclickid`)
  ) ENGINE = MyISAM;
  
  
INSERT INTO qu_pap_monthlyclicks_temp 
(`userid`, `bannerid`, `parentbannerid`, `campaignid`, `countrycode`, `cdata1`, `cdata2`, `channel`, `month`, 
`raw_1`, `raw_2`, `raw_3`, `raw_4`, `raw_5`, `raw_6`, `raw_7`, `raw_8`, `raw_9`, `raw_10`, `raw_11`, `raw_12`, `raw_13`, `raw_14`, `raw_15`, `raw_16`, `raw_17`, `raw_18`, `raw_19`, `raw_20`, `raw_21`, `raw_22`, `raw_23`, `raw_24`, `raw_25`, `raw_26`, `raw_27`, `raw_28`, `raw_29`, `raw_30`, `raw_31`,        
`unique_1`, `unique_2`, `unique_3`, `unique_4`, `unique_5`, `unique_6`, `unique_7`, `unique_8`, `unique_9`, `unique_10`, `unique_11`, `unique_12`, `unique_13`, `unique_14`, `unique_15`, `unique_16`, `unique_17`, `unique_18`, `unique_19`, `unique_20`, `unique_21`, `unique_22`, `unique_23`, `unique_24`, `unique_25`, `unique_26`, `unique_27`,`unique_28`, `unique_29`, `unique_30`, `unique_31`,
`declined_1`, `declined_2`, `declined_3`, `declined_4`, `declined_5`, `declined_6`, `declined_7`, `declined_8`, `declined_9`, `declined_10`, `declined_11`, `declined_12`, `declined_13`, `declined_14`, `declined_15`, `declined_16`, `declined_17`, `declined_18`, `declined_19`, `declined_20`, `declined_21`, `declined_22`, `declined_23`, `declined_24`, `declined_25`, `declined_26`, `declined_27`, `declined_28`, `declined_29`, `declined_30`, `declined_31`
)
SELECT userid, bannerid, parentbannerid, campaignid, countrycode, cdata1, cdata2, channel, month, 
sum(raw_1) ,sum(raw_2) ,sum(raw_3) ,sum(raw_4) ,sum(raw_5) ,sum(raw_6) ,sum(raw_7) ,sum(raw_8) ,sum(raw_9) ,sum(raw_10) ,sum(raw_11) ,sum(raw_12) ,sum(raw_13) ,sum(raw_14) ,sum(raw_15) ,sum(raw_16) ,sum(raw_17) ,sum(raw_18) ,sum(raw_19) ,sum(raw_20) ,sum(raw_21) ,sum(raw_22) ,sum(raw_23),sum(raw_24) ,sum(raw_25) ,sum(raw_26) ,sum(raw_27),sum(raw_28) ,sum(raw_29) ,sum(raw_30) ,sum(raw_31),
sum(unique_1) ,sum(unique_2) ,sum(unique_3) ,sum(unique_4) ,sum(unique_5) ,sum(unique_6) ,sum(unique_7) ,sum(unique_8) ,sum(unique_9) ,sum(unique_10) ,sum(unique_11) ,sum(unique_12) ,sum(unique_13) ,sum(unique_14) ,sum(unique_15) ,sum(unique_16) ,sum(unique_17) ,sum(unique_18) ,sum(unique_19) ,sum(unique_20) ,sum(unique_21) ,sum(unique_22) ,sum(unique_23) ,sum(unique_24) ,sum(unique_25) ,sum(unique_26) ,sum(unique_27), sum(unique_28) ,sum(unique_29) ,sum(unique_30) ,sum(unique_31),
sum(declined_1),sum(declined_2),sum(declined_3), sum(declined_4) ,sum(declined_5),sum(declined_6),sum(declined_7),sum(declined_8) ,sum(declined_9) ,sum(declined_10) ,sum(declined_11) ,sum(declined_12) ,sum(declined_13) ,sum(declined_14) ,sum(declined_15) ,sum(declined_16) ,sum(declined_17) ,sum(declined_18) ,sum(declined_19) ,sum(declined_20) ,sum(declined_21) ,sum(declined_22) ,sum(declined_23), sum(declined_24) ,sum(declined_25) ,sum(declined_26), sum(declined_27) ,sum(declined_28) ,sum(declined_29) ,sum(declined_30), sum(declined_31)
FROM qu_pap_monthlyclicks
GROUP BY userid, bannerid, parentbannerid, campaignid, cdata1, cdata2, countrycode, month, channel ;

DROP TABLE qu_pap_monthlyclicks;

ALTER TABLE qu_pap_monthlyclicks_temp RENAME TO qu_pap_monthlyclicks;

ALTER TABLE qu_pap_monthlyclicks
  ADD INDEX `IDX_qu_pap_monthlyclicks_1` (`userid` ASC, `campaignid` ASC, `bannerid` ASC, `month` ASC) ,
  ADD INDEX `IDX_qu_pap_monthlyclicks_2` (`campaignid` ASC) ,
  ADD INDEX `IDX_qu_pap_monthlyclicks_3` (`bannerid` ASC) ,
  ADD INDEX `IDX_qu_pap_monthlyclicks_4` (`parentbannerid` ASC) ,
  ADD CONSTRAINT `qu_pap_users_qu_pap_monthlyclicks`
    FOREIGN KEY (`userid` )
    REFERENCES `qu_pap_users` (`userid` ),
  ADD CONSTRAINT `qu_pap_campaigns_qu_pap_monthlyclicks`
    FOREIGN KEY (`campaignid` )
    REFERENCES `qu_pap_campaigns` (`campaignid` ),
  ADD CONSTRAINT `qu_pap_banners_qu_pap_monthlyclicks`
    FOREIGN KEY (`bannerid` )
    REFERENCES `qu_pap_banners` (`bannerid` ),
  ADD CONSTRAINT `qu_pap_banners_qu_pap_monthlyclicks_parent`
    FOREIGN KEY (`parentbannerid` )
    REFERENCES `qu_pap_banners` (`bannerid` );