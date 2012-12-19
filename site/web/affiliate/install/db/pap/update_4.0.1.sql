# ---------------------------------------------------------------------- #
# Script generated with: DeZign for Databases v5.1.1                     #
# Target DBMS:           MySQL 3                                         #
# Project file:          pap4.dez                                        #
# Project name:          Version 2                                       #
# Author:                                                                #
# Script type:           Database creation script                        #
# Created on:            2008-09-30 14:15                                #
# Model version:         Version 2008-09-30                              #
# ---------------------------------------------------------------------- #


# ---------------------------------------------------------------------- #
# Tables                                                                 #
# ---------------------------------------------------------------------- #

# ---------------------------------------------------------------------- #
# Add table "qu_pap_cpmcommissions"                                      #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_cpmcommissions (
    userid CHAR(8) NOT NULL,
    bannerid CHAR(8) NOT NULL,
    count INTEGER COMMENT 'Number of impressions per user and banner. When count = 1000 transaction with CPM commission is created and count is set back to 0.',
    CONSTRAINT PK_qu_pap_cpmcommissions PRIMARY KEY (userid, bannerid)
);
