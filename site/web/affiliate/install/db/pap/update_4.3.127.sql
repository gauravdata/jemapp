-- -----------------------------------------------------
-- Table `pap4`.`qu_pap_accounting`
-- -----------------------------------------------------
CREATE TABLE `qu_pap_accountings` (
  `accountingid` CHAR(8) NOT NULL ,
  `accountid` CHAR(8) NULL ,
  `dateinsterted` DATETIME NULL ,
  `amount` INT NULL ,
  `rtype` CHAR(1) NULL ,
  `merchantnote` TEXT NULL ,
  `systemnote` TEXT NULL ,
  PRIMARY KEY (`accountingid`) ,
  INDEX `fk_qu_pap_accounting_qu_g_accounts1` (`accountid` ASC) ,
  CONSTRAINT `fk_qu_pap_accounting_qu_g_accounts1`
    FOREIGN KEY (`accountid` )
    REFERENCES `qu_g_accounts` (`accountid` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;