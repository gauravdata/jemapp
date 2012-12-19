# ---------------------------------------------------------------------- #
# Script generated with: DeZign for Databases v5.1.1                     #
# Target DBMS:           MySQL 3                                         #
# Project file:          pap4.dez                                        #
# Project name:          Version 2                                       #
# Author:                                                                #
# Script type:           Database creation script                        #
# Created on:            2008-11-10 12:53                                #
# Model version:         Version 2008-11-10                              #
# ---------------------------------------------------------------------- #


# ---------------------------------------------------------------------- #
# Tables                                                                 #
# ---------------------------------------------------------------------- #

# ---------------------------------------------------------------------- #
# Add table "qu_pap_bannersinrotators"                                   #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_bannersinrotators (
    bannerinrotatorid CHAR(8) NOT NULL,
    parentbannerid CHAR(8),
    rotatedbannerid CHAR(8),
    all_imps INTEGER,
    uniq_imps VARCHAR(40),
    clicks INTEGER,
    rank FLOAT,
    CONSTRAINT PK_qu_pap_bannersinrotators PRIMARY KEY (bannerinrotatorid)
);
