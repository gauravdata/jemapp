CREATE TABLE qu_pap_campaignattributes (
    attributeid CHAR(8) NOT NULL,
    campaignid CHAR(8),
    name VARCHAR(40),
    value TEXT,
    CONSTRAINT PK_qu_pap_campaignattributes PRIMARY KEY (attributeid)
);

ALTER TABLE qu_pap_campaignattributes ADD CONSTRAINT qu_pap_campaigns_qu_pap_campaignattributes FOREIGN KEY (campaignid) REFERENCES qu_pap_campaigns (campaignid);