# ---------------------------------------------------------------------- #
# Add table "qu_pap_visitors"                                            #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_visitors (
    visitorid VARCHAR(36) NOT NULL,
    name VARCHAR(100),
    email VARCHAR(60),
    CONSTRAINT PK_qu_pap_visitors PRIMARY KEY (visitorid)
);

# ---------------------------------------------------------------------- #
# Add table "qu_pap_visitoraffiliates"                                   #
# ---------------------------------------------------------------------- #

CREATE TABLE qu_pap_visitoraffiliates (
    visitoraffiliateid INTEGER NOT NULL AUTO_INCREMENT,
    visitorid VARCHAR(36) NOT NULL,
    userid CHAR(8) NOT NULL,
    bannerid CHAR(8),
    campaignid CHAR(8) NOT NULL,
    ip VARCHAR(39),
    datevisit DATETIME,
    referrerurl TEXT,
    data1 VARCHAR(40),
    data2 VARCHAR(40),
    CONSTRAINT PK_qu_pap_visitoraffiliates PRIMARY KEY (visitoraffiliateid)
);

ALTER TABLE qu_pap_visitoraffiliates ADD CONSTRAINT qu_pap_visitors_qu_pap_visitoraffiliates 
    FOREIGN KEY (visitorid) REFERENCES qu_pap_visitors (visitorid);

ALTER TABLE qu_pap_visitoraffiliates ADD CONSTRAINT qu_pap_users_qu_pap_visitoraffiliates 
    FOREIGN KEY (userid) REFERENCES qu_pap_users (userid);

ALTER TABLE qu_pap_visitoraffiliates ADD CONSTRAINT qu_pap_banners_qu_pap_visitoraffiliates 
    FOREIGN KEY (bannerid) REFERENCES qu_pap_banners (bannerid);

ALTER TABLE qu_pap_visitoraffiliates ADD CONSTRAINT qu_pap_campaigns_qu_pap_visitoraffiliates 
    FOREIGN KEY (campaignid) REFERENCES qu_pap_campaigns (campaignid);