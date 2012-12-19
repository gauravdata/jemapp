CREATE TABLE qu_pap_commissiontypeattributes (
    attributeid CHAR(8) NOT NULL,
    commtypeid CHAR(8),
    name VARCHAR(40),
    value TEXT,
    CONSTRAINT PK_qu_pap_commissiontypeattributes PRIMARY KEY (attributeid)
);

ALTER TABLE qu_pap_commissiontypeattributes ADD CONSTRAINT qu_pap_commissiontypes_qu_pap_commissiontypeattributes 
    FOREIGN KEY (commtypeid) REFERENCES qu_pap_commissiontypes (commtypeid);