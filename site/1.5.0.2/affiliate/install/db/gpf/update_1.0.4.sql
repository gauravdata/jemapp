# ---------------------------------------------------------------------- #
# Script generated with: DeZign for Databases v5.1.1                     #
# Target DBMS:           MySQL 3                                         #
# Project file:          framework.dez                                   #
# Project name:          Version 2                                       #
# Author:                                                                #
# Script type:           Database creation script                        #
# Created on:            2008-11-14 09:26                                #
# Model version:         Version 2008-11-14                              #
# ---------------------------------------------------------------------- #

# Drop constraints #

ALTER TABLE qu_g_longtasks DROP PRIMARY KEY;

# Drop table #

DROP TABLE qu_g_longtasks;

# ---------------------------------------------------------------------- #
# Tables                                                                 #
# ---------------------------------------------------------------------- #

# ---------------------------------------------------------------------- #
# Add table "qu_g_tasks"                                                 #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_g_tasks (
    taskid CHAR(8) NOT NULL,
    accountid CHAR(8),
    classname VARCHAR(120),
    params TEXT,
    progress TEXT,
    datecreated DATETIME,
    datechanged DATETIME,
    datefinished DATETIME,
    pid VARCHAR(40),
    CONSTRAINT PK_qu_g_tasks PRIMARY KEY (taskid)
);

# ---------------------------------------------------------------------- #
# Add table "qu_g_plannedtasks"                                          #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_g_plannedtasks (
    plannedtaskid CHAR(8) NOT NULL,
    accountid CHAR(8),
    recurrencepresetid CHAR(8),
    classname VARCHAR(120),
    params TEXT,
    lastplandate DATETIME,
    CONSTRAINT PK_qu_g_plannedtasks PRIMARY KEY (plannedtaskid)
);

# ---------------------------------------------------------------------- #
# Add table "qu_g_recurrencepresets"                                     #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_g_recurrencepresets (
    recurrencepresetid CHAR(8) NOT NULL,
    accountid CHAR(8),
    name VARCHAR(80),
    type CHAR(1) COMMENT 'D - default, U - user',
    startdate DATETIME,
    enddate DATETIME,
    CONSTRAINT PK_qu_g_recurrencepresets PRIMARY KEY (recurrencepresetid)
);

# ---------------------------------------------------------------------- #
# Add table "qu_g_recurrencesettings"                                    #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_g_recurrencesettings (
    recurrencesettingid CHAR(8) NOT NULL,
    recurrencepresetid CHAR(8),
    type CHAR(1) COMMENT 'O, E, H, D, W , M, Y',
    period TIMESTAMP,
    frequency INTEGER,
    CONSTRAINT PK_qu_g_recurrencesettings PRIMARY KEY (recurrencesettingid)
);

# ---------------------------------------------------------------------- #
# Foreign key constraints                                                #
# ---------------------------------------------------------------------- #

ALTER TABLE qu_g_plannedtasks ADD CONSTRAINT qu_g_recurrencepresets_qu_g_plannedtasks 
    FOREIGN KEY (recurrencepresetid) REFERENCES qu_g_recurrencepresets (recurrencepresetid);

ALTER TABLE qu_g_recurrencesettings ADD CONSTRAINT qu_g_recurrencepresets_qu_g_recurrencesettings 
    FOREIGN KEY (recurrencepresetid) REFERENCES qu_g_recurrencepresets (recurrencepresetid);
