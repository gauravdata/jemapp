CREATE TABLE qu_pap_affiliatetrackingcodes (
    affiliatetrackingcodeid CHAR(8) NOT NULL,
    userid CHAR(8),
    commtypeid CHAR(8),
    code LONGTEXT,
    note LONGTEXT,
    rstatus CHAR(1),
    CONSTRAINT PK_qu_pap_affiliatetrackingcodes PRIMARY KEY (affiliatetrackingcodeid)
);

ALTER TABLE qu_pap_affiliatetrackingcodes ADD CONSTRAINT qu_pap_commissiontypes_qu_pap_affiliatetrackingcodes 
    FOREIGN KEY (commtypeid) REFERENCES qu_pap_commissiontypes (commtypeid);

ALTER TABLE qu_pap_affiliatetrackingcodes ADD CONSTRAINT qu_pap_users_qu_pap_affiliatetrackingcodes 
    FOREIGN KEY (userid) REFERENCES qu_pap_users (userid);
