DROP TABLE IF EXISTS qu_pap_cachedbanners;

CREATE  TABLE IF NOT EXISTS `qu_pap_cachedbanners` (
  `cachedbannerid` INT NOT NULL AUTO_INCREMENT ,
  `userid` CHAR(8) NULL ,
  `bannerid` CHAR(8) NULL ,
  `parentbannerid` CHAR(8) NULL ,
  `channel` CHAR(10) NULL ,
  `wrapper` VARCHAR(8) NULL ,
  `headers` LONGTEXT NULL ,
  `code` LONGTEXT NULL ,
  `rank` FLOAT NULL DEFAULT 100 ,
  PRIMARY KEY (`cachedbannerid`) ,
  INDEX `IDX_cachedbanners` (`bannerid` ASC, `userid` ASC, `wrapper` ASC, `channel` ASC, `parentbannerid` ASC) ,
  INDEX `IDX_cachedbannersuserid` (`userid` ASC) )
ENGINE = MyISAM;
