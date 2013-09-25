CREATE  TABLE IF NOT EXISTS `qu_g_hierarchicaldatanodes` (
  `nodeid` INT NOT NULL AUTO_INCREMENT COMMENT 'node id' ,
  `type` CHAR(8) NOT NULL COMMENT 'node type - used to identify nodes of one type (for example for one plugin)' ,
  `code` INT NOT NULL COMMENT 'unique code in tree' ,
  `name` CHAR(200) NULL COMMENT 'name od the node' ,
  `lft` INT NOT NULL COMMENT 'nested tree algorithm data - left' ,
  `rgt` INT NOT NULL COMMENT 'nested tree algorithm data - right' ,
  `state` CHAR(1) NOT NULL COMMENT 'state of the node - defined in child class' ,
  `dateinserted` DATETIME NOT NULL ,
  PRIMARY KEY (`nodeid`) );