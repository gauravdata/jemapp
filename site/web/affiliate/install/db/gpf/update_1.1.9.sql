ALTER TABLE  `qu_g_tasks` ADD  `workingareafrom` INT NOT NULL DEFAULT  '0',ADD  `workingareato` INT NOT NULL DEFAULT  '0';
CREATE TABLE IF NOT EXISTS `qu_g_jobsruns` (
  `runid` int(11) NOT NULL AUTO_INCREMENT,
  `starttime` datetime NOT NULL,
  PRIMARY KEY (`runid`));
