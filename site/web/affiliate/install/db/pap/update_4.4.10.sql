-- -----------------------------------------------------
-- Table `qu_pap_cachedbanners`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_cachedbanners` (
  `cachedbannerid` INT NOT NULL AUTO_INCREMENT ,
  `userid` CHAR(8) NULL ,
  `bannerid` CHAR(8) NULL ,
  `parentbannerid` CHAR(8) NULL ,
  `channel` CHAR(10) NULL ,
  `wrapper` VARCHAR(8) NULL ,
  `validto` DATETIME NULL ,
  `code` LONGTEXT NULL ,
  PRIMARY KEY (`cachedbannerid`) )
ENGINE = MyISAM;
