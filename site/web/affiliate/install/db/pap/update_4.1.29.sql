ALTER TABLE qu_pap_bannersinrotators CHANGE uniq_imps uniq_imps INT(11) DEFAULT '0';

ALTER TABLE qu_pap_banners ADD wrapperid CHAR(8) ASCII AFTER campaignid;

CREATE TABLE qu_pap_bannerwrappers (
    wrapperid CHAR(8) NOT NULL,
    name VARCHAR(80),
    code LONGTEXT,
    CONSTRAINT PK_qu_pap_bannerwrappers PRIMARY KEY (wrapperid)
);

ALTER TABLE qu_pap_banners ADD CONSTRAINT qu_pap_bannerwrappers_qu_pap_banners FOREIGN KEY (wrapperid) REFERENCES qu_pap_bannerwrappers (wrapperid);