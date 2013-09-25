# ---------------------------------------------------------------------- #
# Script generated with: DeZign for Databases v5.1.1                     #
# Target DBMS:           MySQL 3                                         #
# Project file:          pap4.dez                                        #
# Project name:          Version 2                                       #
# Author:                                                                #
# Script type:           Database creation script                        #
# Created on:            2008-11-05 11:11                                #
# Model version:         Version 2008-11-05                              #
# ---------------------------------------------------------------------- #


# ---------------------------------------------------------------------- #
# Tables                                                                 #
# ---------------------------------------------------------------------- #

# ---------------------------------------------------------------------- #
# Add table "qu_pap_rules"                                               #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_rules (
    ruleid INTEGER NOT NULL AUTO_INCREMENT,
    campaignid CHAR(8) NOT NULL,
    what VARCHAR(1) NOT NULL COMMENT 'S - number of sales C - value of commissions T- value of total cost',
    status VARCHAR(1) NOT NULL COMMENT 'A - Approved P - Pending D - Declined O - Approved or Pending',
    date VARCHAR(3) NOT NULL COMMENT 'AM - Actual month AY - Actual year AUC - All unpaid commissions LW - Last week LTW - Last two weeks LM - Last month AT - All time SD - Since day of last month',
    since INTEGER,
    equation VARCHAR(1) NOT NULL COMMENT 'L - lower than H - higher than B - between E - equal to',
    equationvalue1 FLOAT NOT NULL,
    equationvalue2 FLOAT,
    action VARCHAR(3) NOT NULL COMMENT 'CG - put affiliate into commission group CGR - put affiliate into commission group (retroactively) BC - add bonus commission',
    commissiongroupid VARCHAR(8),
    bonustype VARCHAR(1) COMMENT '$ %',
    bonusvalue FLOAT,
    CONSTRAINT PK_qu_pap_rules PRIMARY KEY (ruleid)
);