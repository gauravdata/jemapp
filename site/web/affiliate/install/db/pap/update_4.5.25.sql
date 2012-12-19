-- -----------------------------------------------------
-- Table `qu_pap_campaignscategories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qu_pap_campaignscategories` (
  `categoryid` INT NOT NULL ,
  `description` VARCHAR(255) NULL ,
  `image` VARCHAR(255) NULL ,
  PRIMARY KEY (`categoryid`) );

-- -----------------------------------------------------
-- Table `qu_pap_campaignincategory`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `qu_pap_campaignincategory` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `categoryid` INT NOT NULL ,
  `campaignid` CHAR(8) NOT NULL ,
  PRIMARY KEY (`id`) );