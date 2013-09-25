# ---------------------------------------------------------------------- #
# Modify table "qu_pap_commissiontypes"                                  #
# ---------------------------------------------------------------------- #

ALTER TABLE qu_pap_commissiontypes
 CHANGE zeroorderscommission zeroorderscommission CHAR(1) ASCII COMMENT 'commissions on zero orders Y or N' AFTER code,
 CHANGE recurrencetype recurrencepresetid CHAR(8) ASCII;

# ---------------------------------------------------------------------- #
# Add table "qu_pap_recurringcommissions"                                #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_recurringcommissions (
    recurringcommissionid CHAR(8) NOT NULL,
    transid CHAR(8),
    recurrencepresetid CHAR(8),
    commtypeid CHAR(8),
    rstatus CHAR(1) COMMENT 'A P D',
    lastcommissiondate DATETIME,
    CONSTRAINT PK_qu_pap_recurringcommissions PRIMARY KEY (recurringcommissionid)
);

# ---------------------------------------------------------------------- #
# Add table "qu_pap_recurringcommissionentries"                          #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_recurringcommissionentries (
    recurringcommissionentryid CHAR(8) NOT NULL,
    recurringcommissionid CHAR(8),
    userid CHAR(8),
    tier INTEGER,
    commission FLOAT,
    CONSTRAINT PK_qu_pap_recurringcommissionentries PRIMARY KEY (recurringcommissionentryid)
);

# ---------------------------------------------------------------------- #
# Foreign key constraints                                                #
# ---------------------------------------------------------------------- #

ALTER TABLE qu_pap_recurringcommissionentries ADD CONSTRAINT qu_pap_recurringcommissions_qu_pap_recurringcommissionentries 
    FOREIGN KEY (recurringcommissionid) REFERENCES qu_pap_recurringcommissions (recurringcommissionid);
