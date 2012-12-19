DROP TABLE IF EXISTS `qu_pap_visits`;
DROP TABLE IF EXISTS `qu_pap_visitors`;
DROP TABLE IF EXISTS `qu_pap_visitoraffiliates`; 

ALTER TABLE qu_pap_transactions ADD visitorid CHAR(36) NULL;

-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_visitors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_visitors` (
  `visitorid` VARCHAR(36) NOT NULL ,
  `name` VARCHAR(100) NULL DEFAULT NULL ,
  `email` VARCHAR(60) NULL DEFAULT NULL ,
  PRIMARY KEY (`visitorid`) );


-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_visitoraffiliates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_visitoraffiliates` (
  `visitoraffiliateid` INT NOT NULL AUTO_INCREMENT ,
  `visitorid` VARCHAR(36) NOT NULL ,
  `userid` CHAR(8) NULL ,
  `bannerid` CHAR(8) NULL DEFAULT NULL ,
  `campaignid` CHAR(8) NULL ,
  `channelid` VARCHAR(10) NULL ,
  `rtype` CHAR(1) NULL ,
  `ip` VARCHAR(39) NULL DEFAULT NULL ,
  `datevisit` DATETIME NULL DEFAULT NULL ,
  `validto` DATETIME NULL DEFAULT NULL ,
  `referrerurl` TEXT NULL DEFAULT NULL ,
  `data1` VARCHAR(40) NULL DEFAULT NULL ,
  `data2` VARCHAR(40) NULL DEFAULT NULL ,
  PRIMARY KEY (`visitoraffiliateid`) ,
  INDEX `qu_pap_visitors_qu_pap_visitoraffiliates` (`visitorid` ASC) ,
  INDEX `qu_pap_users_qu_pap_visitoraffiliates` (`userid` ASC) ,
  INDEX `qu_pap_banners_qu_pap_visitoraffiliates` (`bannerid` ASC) ,
  INDEX `qu_pap_campaigns_qu_pap_visitoraffiliates` (`campaignid` ASC) ,
  CONSTRAINT `qu_pap_visitors_qu_pap_visitoraffiliates`
    FOREIGN KEY (`visitorid` )
    REFERENCES `qu_pap_visitors` (`visitorid` ),
  CONSTRAINT `qu_pap_users_qu_pap_visitoraffiliates`
    FOREIGN KEY (`userid` )
    REFERENCES `qu_pap_users` (`userid` ),
  CONSTRAINT `qu_pap_banners_qu_pap_visitoraffiliates`
    FOREIGN KEY (`bannerid` )
    REFERENCES `qu_pap_banners` (`bannerid` ),
  CONSTRAINT `qu_pap_campaigns_qu_pap_visitoraffiliates`
    FOREIGN KEY (`campaignid` )
    REFERENCES `qu_pap_campaigns` (`campaignid` ));

-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_visits0`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_visits0` (
  `visitid` INT NOT NULL AUTO_INCREMENT ,
  `visitorid` CHAR(36) NULL DEFAULT NULL ,
  `datevisit` DATETIME NULL DEFAULT NULL ,
  `url` TEXT NULL DEFAULT NULL ,
  `referrerurl` TEXT NULL DEFAULT NULL ,
  `get` TEXT NULL DEFAULT NULL ,
  `anchor` TEXT NULL DEFAULT NULL ,
  `sale` TEXT NULL DEFAULT NULL ,
  `cookies` TEXT NULL ,
  `ip` CHAR(39) NULL DEFAULT NULL ,
  `useragent` TEXT NULL DEFAULT NULL ,
  `trackmethod` CHAR(1) NULL ,
  PRIMARY KEY (`visitid`) );

-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_visits1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_visits1` (
  `visitid` INT NOT NULL AUTO_INCREMENT ,
  `visitorid` CHAR(36) NULL DEFAULT NULL ,
  `datevisit` DATETIME NULL DEFAULT NULL ,
  `url` TEXT NULL DEFAULT NULL ,
  `referrerurl` TEXT NULL DEFAULT NULL ,
  `get` TEXT NULL DEFAULT NULL ,
  `anchor` TEXT NULL DEFAULT NULL ,
  `sale` TEXT NULL DEFAULT NULL ,
  `cookies` TEXT NULL ,
  `ip` CHAR(39) NULL DEFAULT NULL ,
  `useragent` TEXT NULL DEFAULT NULL ,
  `trackmethod` CHAR(1) NULL ,
  PRIMARY KEY (`visitid`) );


-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_visits2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `qu_pap_visits2` (
  `visitid` INT NOT NULL AUTO_INCREMENT ,
  `visitorid` CHAR(36) NULL DEFAULT NULL ,
  `datevisit` DATETIME NULL DEFAULT NULL ,
  `url` TEXT NULL DEFAULT NULL ,
  `referrerurl` TEXT NULL DEFAULT NULL ,
  `get` TEXT NULL DEFAULT NULL ,
  `anchor` TEXT NULL DEFAULT NULL ,
  `sale` TEXT NULL DEFAULT NULL ,
  `cookies` TEXT NULL ,
  `ip` CHAR(39) NULL DEFAULT NULL ,
  `useragent` TEXT NULL DEFAULT NULL ,
  `trackmethod` CHAR(1) NULL ,
  PRIMARY KEY (`visitid`) );

